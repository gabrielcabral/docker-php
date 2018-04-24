<?php

/**
 * Form de Filtro Curso
 *
 * @author leidison.barbosa
 * @since 21/09/2016
 */
class EmitirCertificado_FormFilter extends Fnde_Form
{

    public function __construct()
    {

        $this->setAttrib("class", "labelLongo");

        $uf = $this->createElement("select", "SG_UF", array("label" => "UF: "))
            ->addMultiOption(0, "Selecione");

        $mesorregiao = $this->createElement('select', 'CO_MESORREGIAO', array("label" => "Mesorregião: "))
            ->addMultiOption(0, 'Selecione');

        $municipio = $this->createElement('select', 'CO_MUNICIPIO', array("label" => "Município: "))
            ->addMultiOption(0, 'Selecione');

        $nome = $this->createElement('text', 'NO_USUARIO', array("label" => "Nome: ", "maxlength" => "150", "size" => "50"));

        $cpf = $this->createElement('text', 'NU_CPF', array("label" => "CPF: ", "class" => "cpf"));

        $curso = $this->createElement('select', 'NU_SEQ_CURSO', array("label" => "Curso: "))
            ->addMultiOption(0, 'Selecione');

        $perfilForm = $this->createElement('select', 'NU_SEQ_TIPO_PERFIL', array("label" => "Perfil: "))
            ->setRequired(true);

        $this->getPerfisPorPermissao($perfilForm);

        $btConfirmar = $this->createElement('submit', 'confirmar',
            array("label" => "Confirmar", "value" => "Confirmar", "class" => "btnConfirmar", "title" => "Confirmar"));

        $btCancelar = $this->createElement('button', 'cancelar',
            array("label" => "Cancelar", "value" => "Cancelar", "class" => "btnCancelar", "title" => "Cancelar"));

        $this->addElements(array($uf, $mesorregiao, $municipio, $nome, $cpf, $curso, $perfilForm, $btConfirmar, $btCancelar));

        $this->addDisplayGroup(
            array('SG_UF', 'CO_MESORREGIAO', 'CO_MUNICIPIO', 'NO_USUARIO', 'NU_CPF', 'NU_SEQ_CURSO', 'NU_SEQ_TIPO_PERFIL'), 'dadoscurso',
            array("legend" => "Filtro"));

        $obDisplayGroup = $this->addDisplayGroup(array('confirmar', 'cancelar'), 'botoes',
            array('class' => 'agrupador barraBtsAcoes'));

        parent::__construct();

        $obDisplayGroup->botoes->addDecorator('HtmlTag',
            array('tag' => 'div', 'id' => 'divBotoes', 'class' => 'barraBtsAcoes'));
        $obDisplayGroup->botoes->removeDecorator('fieldset');
    }

    public function populate(array $dados)
    {

        parent::populate($dados);

    }

    public function carregaCursos(array $dados)
    {
        $cursoBusiness = new Fnde_Sice_Business_Curso();

        $business = new Fnde_Sice_Business_EmitirCertificado();
        $permissoes = $business->permissoes();

        $cursos = array();

        if ($permissoes['estado'] == 'todos') {
            if (!empty($dados['SG_UF'])) {
                $cursos = $cursoBusiness->getCursoPorUf($dados['SG_UF']);
            }
        } else {
            $cursos = $cursoBusiness->getCursoPorUf($permissoes['estado']);
        }
        foreach ($cursos as $curso) {
            $this->NU_SEQ_CURSO->addMultiOption($curso['NU_SEQ_CURSO'], $curso['DS_NOME_CURSO']);
        }
    }

    public function getPerfisPorPermissao($perfilForm)
    {
        $usuario = Zend_Auth::getInstance()->getIdentity();
        if (in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_ADMINISTRADOR,
                $usuario->credentials)
            || in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_EQUIPE,
                $usuario->credentials)
            || in_array(Fnde_Sice_Business_Componentes::COORDENADOR_NACIONAL_GESTOR,
                $usuario->credentials)
            || in_array(Fnde_Sice_Business_Componentes::ARTICULADOR,
                $usuario->credentials)
        ) {
            $perfilForm->addMultiOption(null,
                array('' => 'Selecione',
                    Fnde_Sice_Business_PerfilUsuario::ARTICULADOR => 'Articulador',
                    Fnde_Sice_Business_PerfilUsuario::CURSISTA => 'Cursista',
                    Fnde_Sice_Business_PerfilUsuario::TUTOR => 'Tutor'
                ));
        } else if (in_array(Fnde_Sice_Business_Componentes::TUTOR,
            $usuario->credentials)) {
            $perfilForm->addMultiOption(null,
                array('' => 'Selecione',
                    Fnde_Sice_Business_PerfilUsuario::CURSISTA => 'Cursista',
                    Fnde_Sice_Business_PerfilUsuario::TUTOR => 'Tutor'
                ));
        } else {
            $perfilForm->addMultiOption(null,
                array('' => 'Selecione',
                    Fnde_Sice_Business_PerfilUsuario::CURSISTA => 'Cursista'
                ));
        }
    }

    public function encadeiaCombos($dados)
    {

        $business = new Fnde_Sice_Business_EmitirCertificado();
        $permissoes = $business->permissoes();

        $this->carregaUF($permissoes);

        $this->encadeiaMesorregiao($dados, $permissoes);

        $this->encadeiaMunicipio($dados, $permissoes);
    }

    /**
     * Apenas
     * COORDENADOR_NACIONAL_ADMINISTRADOR
     * COORDENADOR_NACIONAL_EQUIPE
     * COORDENADOR_NACIONAL_GESTOR
     *
     * @param $permissoes
     */

    public function carregaUF($permissoes)
    {

        if ($permissoes['estado'] == 'todos') {
            $businessUF = new Fnde_Sice_Business_Uf();
            $ufs = $businessUF->search(array('SG_UF'));
            $qtd = count($ufs);
            for ($i = 0; $i < $qtd; $i++) {
                $this->SG_UF->addMultiOption($ufs[$i]['SG_UF'], $ufs[$i]['SG_UF']);
            }
        } else {
            $this->SG_UF->addMultiOption($permissoes['estado'], $permissoes['estado'])
                ->setValue($permissoes['estado'])
                ->setAttrib('readonly', 'readonly');
        }

    }

    /**
     * Apenas
     * COORDENADOR_NACIONAL_ADMINISTRADOR
     * COORDENADOR_NACIONAL_EQUIPE
     * COORDENADOR_NACIONAL_GESTOR
     * COORDENADOR_EXECUTIVO_ESTADUAL
     * COORDENADOR_ESTADUAL
     *
     * @param $dados
     * @param $permissoes
     */
    public function encadeiaMesorregiao($dados, $permissoes)
    {
        $businessMesoregiao = new Fnde_Sice_Business_MesoRegiao();
        if (in_array($permissoes['mesorregiao'], array('todas', 'do_estado'))) {

            if ($permissoes['estado'] == 'todos' && !empty($dados['SG_UF'])) {
                $result = $businessMesoregiao->getMesoRegiaoPorUF($dados['SG_UF']);
            } else if ($permissoes['estado'] != 'todos') {
                $result = $businessMesoregiao->getMesoRegiaoPorUF($permissoes['estado']);
            } else {
                $result = array();
            }

            $qtd = count($result);
            for ($i = 0; $i < $qtd; $i++) {
                $this->CO_MESORREGIAO->addMultiOption($result[$i]['CO_MESO_REGIAO'], $result[$i]['NO_MESO_REGIAO']);
            }
        } else {
            $meso = $businessMesoregiao->getMesoRegiaoById($permissoes['mesorregiao']);
            $this->CO_MESORREGIAO->addMultiOption($meso['CO_MESO_REGIAO'], $meso['NO_MESO_REGIAO']);
        }
    }

    /**
     * Menos o CURSISTA
     *
     * @param $dados
     * @param $permissoes
     */
    public function encadeiaMunicipio($dados, $permissoes)
    {
        $businessMesoregiao = new Fnde_Sice_Business_MesoRegiao();
        $businessUF = new Fnde_Sice_Business_Uf();

        $municipios = array();

        switch ($permissoes['municipio']) {

            case 'todos':

                if (!empty($dados['CO_MESORREGIAO'])) {
                    $municipios = $businessMesoregiao->getMunicipioPorMesoRegiao($dados['CO_MESORREGIAO']);
                } else if (!empty($dados['SG_UF'])) {
                    $municipios = $businessUF->getMunicipioPorUf($dados['SG_UF']);
                }
                break;

            case 'do_estado':

                if (!empty($dados['CO_MESORREGIAO'])) {
                    $municipios = $businessMesoregiao->getMunicipioPorMesoRegiao($dados['CO_MESORREGIAO']);
                } else {
                    $municipios = $businessUF->getMunicipioPorUf($permissoes['estado']);
                }
                break;

            case 'da_mesorregiao':

                $municipios = $businessMesoregiao->getMunicipioPorMesoRegiao($permissoes['mesorregiao']);
                break;
        }

        foreach ($municipios as $municipio) {
            $this->CO_MUNICIPIO->addMultiOption($municipio['CO_MUNICIPIO_IBGE'], $municipio['NO_MUNICIPIO']);
        }

    }
}
