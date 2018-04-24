<?php

class TermoCompromisso_Form extends Fnde_Form
{

    public function __construct()
    {

    }

    public function assinar() {
        $this->setAttrib('id', 'form')->setAction($this->getView()->baseUrl().'/index.php/manutencao/termocompromisso/assinar');

        //elementos
        $co_acao = $this->createElement('radio','co_acao');
        $co_acao->setLabel('A��es')
            ->addMultiOptions(array(
                'CB' => 'Concordo com o Termo de Compromisso e opto por receber bolsa.',
                'SB' => 'Concordo com o Termo de Compromisso e opto por N�O receber bolsa.',
                'AV' => 'N�o concordo com o Termo de Compromisso. '
            ));

        // Adiciona os elementos ao formul�rio
        $this->addElements(array($co_acao));

        $btConfirmar = $this->createElement('submit', 'confirmar',
            array("label" => "Confirmar", "value" => "Confirmar", "class" => "btnConfirmar", "title" => "Confirmar"));

        //Adicionado Componentes no formul�rio
        $this->addElements(array($btConfirmar));

        parent::__construct();

        return $this;
    }


    public function termo($dadosUsuario)
    {
        //marca a op��o de papel desempenhado
        $papel = "";
        $tarefasPerfil = "";
        $tarefas = "";

        switch ($dadosUsuario['DS_TIPO_PERFIL_SEGWEB']) {
            case Fnde_Sice_Model_TipoPerfil::DS_TIPO_PERFIL_TUTOR:
                $papel = "tutor";
                $tarefasPerfil = "Tutor:";
                $tarefas = "
                a. apresentar cronograma de realiza��o do(s) curso(s) para o articulador e a coordena��o estadual;<br/>
                b. promover e divulgar o Programa nas comunidades escolar e local, destacando seus objetivos, crit�rios de participa��o e per�odo de inscri��o;<br/>
                c. orientar os interessados no(s) curso(s) sobre os procedimentos de pr�-matr�cula e de matr�cula;<br/>
                d. comunicar aos inscritos a confirma��o da matr�cula no(s) curso(s), bem como informar local e hor�rio da realiza��o de encontros presenciais;<br/>
                e. conhecer o funcionamento e a metodologia do curso, bem como socializar essas informa��es;<br/>
                f. indicar e orientar os cursistas sobre o material did�tico do curso, sobre o ambiente virtual de aprendizagem e o Sistema de Informa��o do Programa Nacional de Fortalecimento dos Conselhos Escolares (SICE);<br/>
                g. organizar os encontros presenciais em articula��o com as secretarias de educa��o, com os articuladores e a coordena��o estadual, indicando localidade e infraestrutura adequadas � realiza��o dos eventos;<br/>
                h. promover a socializa��o e o debate de experi�ncias em rela��o aos cursos, refor�ando sempre a autonomia dos cursistas na busca de solu��es criativas e pertinentes a sua realidade;<br/>
                i. acompanhar t�cnica e pedagogicamente o processo de forma��o dos cursistas;<br/>
                j. elaborar plano de acompanhamento pedag�gico dos cursistas;<br/>
                k. acompanhar as atividades presenciais e a dist�ncia dos cursistas sob sua orienta��o;<br/>
                l. elaborar e enviar para o articulador e a coordena��o estadual os documentos de acompanhamento das atividades dos cursistas sob sua orienta��o, sempre que solicitado;<br/>
                m. controlar a frequ�ncia dos cursistas nos momentos presenciais, receber e avaliar as atividades, dentro do prazo definido no cronograma de execu��o do curso, lan�ando os resultados no SICE, dispon�veis no s�tio do FNDE;<br/>
                n. informar altera��es em seus dados cadastrais e eventuais mudan�as nas condi��es que lhe garantiram inscri��o e perman�ncia na rede de tutoria;<br/>
                o. coletar os dados cadastrais dos cursistas sob sua orienta��o;<br/>
                p. selecionar entre os trabalhos finais dos cursistas conselheiros os mais significativos, para serem encaminhados �s coordena��es estaduais para divulga��o ampla;<br/>
                q. avaliar o processo de forma��o dos cursistas, apresentando sugest�es para o aprimoramento do Programa;<br/>
                r. participar da gest�o do Programa, apresentando dificuldades, problemas e poss�veis solu��es;<br/>
                s. solicitar apoio t�cnico e pedag�gico ao articulador e � coordena��o estadual do Programa, sempre que necess�rio;<br/>
                t. firmar seu pr�prio Termo de Compromisso no SICE, para fins de concess�o de bolsa;<br/>
                u. orientar o processo de levantamento de demandas e cursos, sistematiz�-lo e enviar informa��es � Coordena��o Estadual do Programa.<br/>
                ";
                break;
            case Fnde_Sice_Model_TipoPerfil::DS_TIPO_PERFIL_ARTICULADOR:
                $papel = "articulador";
                $tarefasPerfil = "Articulador:";
                $tarefas = "
                a. promover e divulgar os cursos do Programa, destacando seus objetivos, crit�rios de participa��o e per�odo de inscri��o;<br/>
                b. elaborar em conjunto com a coordena��o estadual o cronograma dos cursos a serem ofertados no ano, em conson�ncia com as diretrizes do MEC;<br/>
                c. auxiliar os tutores nos cursos, tanto na fase presencial quanto a dist�ncia;<br/>
                d. orientar os tutores sobre a execu��o do cronograma dos cursos que ser�o ofertados;<br/>
                e. orientar a elabora��o do plano de acompanhamento pedag�gico das a��es desenvolvidas pelos tutores;<br/>
                f. coordenar e orientar os tutores dos munic�pios atendidos pelo Programa quanto � disponibiliza��o e � utiliza��o dos materiais pedag�gicos;<br/>
                g. organizar, em articula��o com a coordena��o estadual do Programa, os encontros presenciais dos cursos, inclusive os de tutoria, indicando a localidade e infraestrutura adequadas � realiza��o dos eventos;<br/>
                h. promover a socializa��o e o debate de experi�ncias em rela��o aos cursos ofertados nos diferentes munic�pios do estado;<br/>
                i. avaliar o processo de forma��o dos cursistas, juntamente com os tutores, apresentando observa��es sobre os diversos n�veis de desenvolvimento do Programa;<br/>
                j. solicitar apoio t�cnico e pedag�gico � coordena��o estadual, sempre que necess�rio;<br/>
                k. assistir � coordena��o estadual e aos tutores no que concerne � realiza��o dos cursos;<br/>
                l. firmar seu pr�prio Termo de Compromisso no SICE, para fins de concess�o de bolsa;<br/>
                m. monitorar a oficializa��o do Termo de Compromisso do Bolsista dos tutores no SICE.<br/>
                n. orientar o processo de levantamento de demandas e cursos, sistematiz�-lo e enviar informa��es � Coordena��o Estadual do Programa.<br/>
                ";
                break;
            case Fnde_Sice_Model_TipoPerfil::DS_TIPO_PERFIL_COORDENADOREXECUTIVOESTADUAL:
                $papel = "coordenador executivo estadual";
                $tarefasPerfil = "Coordenador Executivo Estadual:";
                $tarefas = "
                a. definir o plano de a��o para a implementa��o do Programa no �mbito do estado (ou do DF), de acordo com as orienta��es da coordena��o nacional;<br/>
                b. realizar a gest�o pedag�gica e administrativo-financeira do Programa e executar todas as a��es pertinentes � coordena��o em sua jurisdi��o;<br/>
                c. estimular a participa��o dos munic�pios do estado no Programa;<br/>
                d. selecionar os candidatos a articuladores e tutores dos cursos oferecidos pelo Programa, respeitando estritamente os pr�-requisitos estabelecidos para cada fun��o, seja quanto � forma��o, seja quanto � experi�ncia exigidas, assegurando publicidade e transpar�ncia ao processo e impedindo que este venha a sofrer interfer�ncias indevidas, relacionadas a la�os de parentesco, afinidade acad�mica ou proximidade pessoal;<br/>
                e. responsabilizar-se pela inser��o completa e correta de seus dados cadastrais, bem como dos dados cadastrais de articuladores e tutores e os dos membros da coordena��o estadual do Programa no Sistema de Informa��o do Programa Nacional de Fortalecimento dos Conselhos Escolares (SICE), disponibilizados nos portais do FNDE e do MEC;<br/>
                f. encaminhar � SEB/MEC, por meio do SICE, os lotes mensais com as solicita��es de pagamento a bolsistas participantes do Programa;<br/>
                g. garantir a atualiza��o mensal, no SICE, de suas informa��es cadastrais bem como as dos demais bolsistas dos Programas;<br/>
                h. apoiar t�cnica e institucionalmente os munic�pios na fase presencial dos cursos;<br/>
                i. articular a forma��o da rede de tutoria em seu estado ou DF, garantindo a forma��o e capacita��o dos tutores;<br/>
                j. dar suporte em rela��o � utiliza��o do SICE e monitorar, sistematicamente, a atualiza��o das informa��es;<br/>
                k. planejar, executar, monitorar e avaliar os trabalhos desenvolvidos nos munic�pios;<br/>
                l. acompanhar e avaliar bolsistas no SICE.<br/>
                m. apoiar a pesquisa avaliativa do Programa, propondo reformula��es pertinentes;<br/>
                n. fazer-se representar nas reuni�es t�cnicas do Programa;<br/>
                o. orientar o processo de levantamento de demandas e cursos, sistematiz�-lo e enviar informa��es � coordena��o nacional do Programa;<br/>
                p. firmar seu pr�prio Termo de Compromisso no SICE, para fins de concess�o de bolsa.<br/>
                q. monitorar a oficializa��o do Termo de Compromisso do Bolsista dos articuladores e tutores no SICE.<br/>
                ";
                break;
        }

        $protocol = isset($_SERVER['HTTPS']) ? 'https' : 'http';
        $baseUrl = $protocol . '://' . $_SERVER['HTTP_HOST'] . str_replace('index.php','',Zend_Controller_Front::getInstance()->getBaseUrl());

        //coloca todas as informa��es vindas do banco em maiusculo
        foreach ($dadosUsuario as &$usuario) {
            $usuario = strtoupper($usuario);
        }

        //busca municipio do endere�o
        $municipio = new Fnde_Sice_Model_Municipio();
        $select = $municipio->select()->where('co_municipio_ibge = ' . $dadosUsuario['CO_MUNICIPIO_ENDERECO']);
        $stmt = $select->query();

        $dadosUsuario['MUNICIPIO'] = $stmt->fetch();

        //formata data
        $dadosUsuario['DT_NASCIMENTO'] = substr($dadosUsuario['DT_NASCIMENTO'], 0, 10);
        $dadosUsuario['DT_EMISSAO_DOCUMENTACAO'] = substr($dadosUsuario['DT_EMISSAO_DOCUMENTACAO'], 0, 10);

        //formata cpf
        $dadosUsuario['NU_CPF'] = substr($dadosUsuario['NU_CPF'], 0, 3) . '.' . substr($dadosUsuario['NU_CPF'], 3, 3) . '.' . substr($dadosUsuario['NU_CPF'], 6, 3) . '-' . substr($dadosUsuario['NU_CPF'], 9, 3);

        //formata cep
        $dadosUsuario['NU_CEP'] = substr($dadosUsuario['NU_CEP'], 0, 5) . '-' . substr($dadosUsuario['NU_CEP'], 5, 3);

        //formata telefone
        $dadosUsuario['DS_TELEFONE_USUARIO'] = '(' . substr($dadosUsuario['DS_TELEFONE_USUARIO'], 0, 2) . ') ' . substr($dadosUsuario['DS_TELEFONE_USUARIO'], 2);
        $dadosUsuario['DS_CELULAR_USUARIO'] = ($dadosUsuario['DS_CELULAR_USUARIO']) ?
            '(' . substr($dadosUsuario['DS_CELULAR_USUARIO'], 0, 2) . ') ' . substr($dadosUsuario['DS_CELULAR_USUARIO'], 2) :
            'n�o possuo';

        //constroi o termo
        $termo = "
            <div align='center'>
            <img src='{$baseUrl}/img/brasao_republica.png' />
            </div>
            <br/>
            <div align='center'>
            MINIST�RIO DA EDUCA��O<br/>
            FUNDO NACIONAL DE DESENVOLVIMENTO DA EDUCA��O<br/>
            CONSELHO DELIBERATIVO<br/>
            </div>
            <br/><br/>

            <div align='center'>
            Programa Nacional de Fortalecimento dos Conselhos Escolares<br/>
            Curso de Forma��o para Conselheiros Escolares<br/>
            Termo de Compromisso do Bolsista<br/>
            Lei N� 11.273/2006
            </div>

            <p align='justify'>
            De acordo com os termos estabelecidos nas normas do Programa Nacional de Fortalecimento dos Conselhos Escolares, desenvolvido pelo Minist�rio da Educa��o e as Secretarias de Educa��o dos Estados e Distrito Federal,
            eu {$dadosUsuario['NO_USUARIO']} nascido em {$dadosUsuario['DT_NASCIMENTO']}, portador do CPF n� {$dadosUsuario['NU_CPF']}, da carteira de identidade n� {$dadosUsuario['NU_IDENTIDADE']}, expedida em {$dadosUsuario['DT_EMISSAO_DOCUMENTACAO']}, por {$dadosUsuario['CO_ORGAO_EMISSOR']}/{$dadosUsuario['SG_UF_EMISSAO_DOC']}, morador no endere�o {$dadosUsuario['DS_ENDERECO']}, bairro {$dadosUsuario['DS_BAIRRO_ENDERECO']} - {$dadosUsuario['MUNICIPIO']['NO_MUNICIPIO']}/{$dadosUsuario['MUNICIPIO']['SG_UF']}, CEP {$dadosUsuario['NU_CEP']}, telefones residencial {$dadosUsuario['DS_TELEFONE_USUARIO']} e comercial {$dadosUsuario['DS_CELULAR_USUARIO']}, e-mail {$dadosUsuario['DS_EMAIL_USUARIO']},
            confirmo estar em condi��es de participar do Programa desempenhando a fun��o de {$papel}.
            </p>

            <p>
            Al�m disso, comprometo-me a:
            </p>

            <p align='justify' style='margin-left: 30px;'>
            - fornecer os documentos comprobat�rios dos requisitos para a inscri��o e perman�ncia no Programa sempre que solicitado;<br/>
            - dedicar-me com afinco �s atividades do Curso de Forma��o para Conselheiros Escolares, conforme compet�ncias espec�ficas definidas nos normativos do Programa; e<br/>
            - n�o acumular mais de uma bolsa de estudo e pesquisa regida pela Lei 11.273/2006.<br/>
            </p>

            <p align='justify'>
            Estou ciente de que, para fazer jus ao recebimento da bolsa de estudo e pesquisa destinada ao {$papel}, devo realizar com dedica��o e efici�ncia todas as atribui��es previstas, entre as quais se destacam:
            </p>

            <p>{$tarefasPerfil}</p>

            <p align='justify' style='margin-left: 30px;'>
            {$tarefas}
            </p>
        ";

        return $termo;
    }

    public function assinatura($dados){
        switch ($dados['CO_ACAO']) {
            case Fnde_Sice_Model_TermoCompromisso::CO_ACAO_COMBOLSA:
                $aceite = "concordar com o Termo de Compromisso e por receber bolsa.";
                break;
            case Fnde_Sice_Model_TermoCompromisso::CO_ACAO_SEMBOLSA:
                $aceite = "concordar com o Termo de Compromisso e por <strong>N�O</strong> receber bolsa.";
                break;
            case Fnde_Sice_Model_TermoCompromisso::CO_ACAO_VIEW:
                $aceite = "n�o concordar com o Termo de Compromisso.";
                break;
        }


        $assinatura = "
            <br/>
            <p>
            Optando por {$aceite}
            </p>

            <br/><br/><br/>
            <div align='center'>
            __________________________________________<br/>
            {$dados['NO_USUARIO']}<br/>
            {$dados['DT_ASSINATURA']}
            </div>
        ";

        return $assinatura;
    }

}