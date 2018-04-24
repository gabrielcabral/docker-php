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
        $co_acao->setLabel('Ações')
            ->addMultiOptions(array(
                'CB' => 'Concordo com o Termo de Compromisso e opto por receber bolsa.',
                'SB' => 'Concordo com o Termo de Compromisso e opto por NÃO receber bolsa.',
                'AV' => 'Não concordo com o Termo de Compromisso. '
            ));

        // Adiciona os elementos ao formulário
        $this->addElements(array($co_acao));

        $btConfirmar = $this->createElement('submit', 'confirmar',
            array("label" => "Confirmar", "value" => "Confirmar", "class" => "btnConfirmar", "title" => "Confirmar"));

        //Adicionado Componentes no formulário
        $this->addElements(array($btConfirmar));

        parent::__construct();

        return $this;
    }


    public function termo($dadosUsuario)
    {
        //marca a opção de papel desempenhado
        $papel = "";
        $tarefasPerfil = "";
        $tarefas = "";

        switch ($dadosUsuario['DS_TIPO_PERFIL_SEGWEB']) {
            case Fnde_Sice_Model_TipoPerfil::DS_TIPO_PERFIL_TUTOR:
                $papel = "tutor";
                $tarefasPerfil = "Tutor:";
                $tarefas = "
                a. apresentar cronograma de realização do(s) curso(s) para o articulador e a coordenação estadual;<br/>
                b. promover e divulgar o Programa nas comunidades escolar e local, destacando seus objetivos, critérios de participação e período de inscrição;<br/>
                c. orientar os interessados no(s) curso(s) sobre os procedimentos de pré-matrícula e de matrícula;<br/>
                d. comunicar aos inscritos a confirmação da matrícula no(s) curso(s), bem como informar local e horário da realização de encontros presenciais;<br/>
                e. conhecer o funcionamento e a metodologia do curso, bem como socializar essas informações;<br/>
                f. indicar e orientar os cursistas sobre o material didático do curso, sobre o ambiente virtual de aprendizagem e o Sistema de Informação do Programa Nacional de Fortalecimento dos Conselhos Escolares (SICE);<br/>
                g. organizar os encontros presenciais em articulação com as secretarias de educação, com os articuladores e a coordenação estadual, indicando localidade e infraestrutura adequadas à realização dos eventos;<br/>
                h. promover a socialização e o debate de experiências em relação aos cursos, reforçando sempre a autonomia dos cursistas na busca de soluções criativas e pertinentes a sua realidade;<br/>
                i. acompanhar técnica e pedagogicamente o processo de formação dos cursistas;<br/>
                j. elaborar plano de acompanhamento pedagógico dos cursistas;<br/>
                k. acompanhar as atividades presenciais e a distância dos cursistas sob sua orientação;<br/>
                l. elaborar e enviar para o articulador e a coordenação estadual os documentos de acompanhamento das atividades dos cursistas sob sua orientação, sempre que solicitado;<br/>
                m. controlar a frequência dos cursistas nos momentos presenciais, receber e avaliar as atividades, dentro do prazo definido no cronograma de execução do curso, lançando os resultados no SICE, disponíveis no sítio do FNDE;<br/>
                n. informar alterações em seus dados cadastrais e eventuais mudanças nas condições que lhe garantiram inscrição e permanência na rede de tutoria;<br/>
                o. coletar os dados cadastrais dos cursistas sob sua orientação;<br/>
                p. selecionar entre os trabalhos finais dos cursistas conselheiros os mais significativos, para serem encaminhados às coordenações estaduais para divulgação ampla;<br/>
                q. avaliar o processo de formação dos cursistas, apresentando sugestões para o aprimoramento do Programa;<br/>
                r. participar da gestão do Programa, apresentando dificuldades, problemas e possíveis soluções;<br/>
                s. solicitar apoio técnico e pedagógico ao articulador e à coordenação estadual do Programa, sempre que necessário;<br/>
                t. firmar seu próprio Termo de Compromisso no SICE, para fins de concessão de bolsa;<br/>
                u. orientar o processo de levantamento de demandas e cursos, sistematizá-lo e enviar informações à Coordenação Estadual do Programa.<br/>
                ";
                break;
            case Fnde_Sice_Model_TipoPerfil::DS_TIPO_PERFIL_ARTICULADOR:
                $papel = "articulador";
                $tarefasPerfil = "Articulador:";
                $tarefas = "
                a. promover e divulgar os cursos do Programa, destacando seus objetivos, critérios de participação e período de inscrição;<br/>
                b. elaborar em conjunto com a coordenação estadual o cronograma dos cursos a serem ofertados no ano, em consonância com as diretrizes do MEC;<br/>
                c. auxiliar os tutores nos cursos, tanto na fase presencial quanto a distância;<br/>
                d. orientar os tutores sobre a execução do cronograma dos cursos que serão ofertados;<br/>
                e. orientar a elaboração do plano de acompanhamento pedagógico das ações desenvolvidas pelos tutores;<br/>
                f. coordenar e orientar os tutores dos municípios atendidos pelo Programa quanto à disponibilização e à utilização dos materiais pedagógicos;<br/>
                g. organizar, em articulação com a coordenação estadual do Programa, os encontros presenciais dos cursos, inclusive os de tutoria, indicando a localidade e infraestrutura adequadas à realização dos eventos;<br/>
                h. promover a socialização e o debate de experiências em relação aos cursos ofertados nos diferentes municípios do estado;<br/>
                i. avaliar o processo de formação dos cursistas, juntamente com os tutores, apresentando observações sobre os diversos níveis de desenvolvimento do Programa;<br/>
                j. solicitar apoio técnico e pedagógico à coordenação estadual, sempre que necessário;<br/>
                k. assistir à coordenação estadual e aos tutores no que concerne à realização dos cursos;<br/>
                l. firmar seu próprio Termo de Compromisso no SICE, para fins de concessão de bolsa;<br/>
                m. monitorar a oficialização do Termo de Compromisso do Bolsista dos tutores no SICE.<br/>
                n. orientar o processo de levantamento de demandas e cursos, sistematizá-lo e enviar informações à Coordenação Estadual do Programa.<br/>
                ";
                break;
            case Fnde_Sice_Model_TipoPerfil::DS_TIPO_PERFIL_COORDENADOREXECUTIVOESTADUAL:
                $papel = "coordenador executivo estadual";
                $tarefasPerfil = "Coordenador Executivo Estadual:";
                $tarefas = "
                a. definir o plano de ação para a implementação do Programa no âmbito do estado (ou do DF), de acordo com as orientações da coordenação nacional;<br/>
                b. realizar a gestão pedagógica e administrativo-financeira do Programa e executar todas as ações pertinentes à coordenação em sua jurisdição;<br/>
                c. estimular a participação dos municípios do estado no Programa;<br/>
                d. selecionar os candidatos a articuladores e tutores dos cursos oferecidos pelo Programa, respeitando estritamente os pré-requisitos estabelecidos para cada função, seja quanto à formação, seja quanto à experiência exigidas, assegurando publicidade e transparência ao processo e impedindo que este venha a sofrer interferências indevidas, relacionadas a laços de parentesco, afinidade acadêmica ou proximidade pessoal;<br/>
                e. responsabilizar-se pela inserção completa e correta de seus dados cadastrais, bem como dos dados cadastrais de articuladores e tutores e os dos membros da coordenação estadual do Programa no Sistema de Informação do Programa Nacional de Fortalecimento dos Conselhos Escolares (SICE), disponibilizados nos portais do FNDE e do MEC;<br/>
                f. encaminhar à SEB/MEC, por meio do SICE, os lotes mensais com as solicitações de pagamento a bolsistas participantes do Programa;<br/>
                g. garantir a atualização mensal, no SICE, de suas informações cadastrais bem como as dos demais bolsistas dos Programas;<br/>
                h. apoiar técnica e institucionalmente os municípios na fase presencial dos cursos;<br/>
                i. articular a formação da rede de tutoria em seu estado ou DF, garantindo a formação e capacitação dos tutores;<br/>
                j. dar suporte em relação à utilização do SICE e monitorar, sistematicamente, a atualização das informações;<br/>
                k. planejar, executar, monitorar e avaliar os trabalhos desenvolvidos nos municípios;<br/>
                l. acompanhar e avaliar bolsistas no SICE.<br/>
                m. apoiar a pesquisa avaliativa do Programa, propondo reformulações pertinentes;<br/>
                n. fazer-se representar nas reuniões técnicas do Programa;<br/>
                o. orientar o processo de levantamento de demandas e cursos, sistematizá-lo e enviar informações à coordenação nacional do Programa;<br/>
                p. firmar seu próprio Termo de Compromisso no SICE, para fins de concessão de bolsa.<br/>
                q. monitorar a oficialização do Termo de Compromisso do Bolsista dos articuladores e tutores no SICE.<br/>
                ";
                break;
        }

        $protocol = isset($_SERVER['HTTPS']) ? 'https' : 'http';
        $baseUrl = $protocol . '://' . $_SERVER['HTTP_HOST'] . str_replace('index.php','',Zend_Controller_Front::getInstance()->getBaseUrl());

        //coloca todas as informações vindas do banco em maiusculo
        foreach ($dadosUsuario as &$usuario) {
            $usuario = strtoupper($usuario);
        }

        //busca municipio do endereço
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
            'não possuo';

        //constroi o termo
        $termo = "
            <div align='center'>
            <img src='{$baseUrl}/img/brasao_republica.png' />
            </div>
            <br/>
            <div align='center'>
            MINISTÉRIO DA EDUCAÇÃO<br/>
            FUNDO NACIONAL DE DESENVOLVIMENTO DA EDUCAÇÃO<br/>
            CONSELHO DELIBERATIVO<br/>
            </div>
            <br/><br/>

            <div align='center'>
            Programa Nacional de Fortalecimento dos Conselhos Escolares<br/>
            Curso de Formação para Conselheiros Escolares<br/>
            Termo de Compromisso do Bolsista<br/>
            Lei N° 11.273/2006
            </div>

            <p align='justify'>
            De acordo com os termos estabelecidos nas normas do Programa Nacional de Fortalecimento dos Conselhos Escolares, desenvolvido pelo Ministério da Educação e as Secretarias de Educação dos Estados e Distrito Federal,
            eu {$dadosUsuario['NO_USUARIO']} nascido em {$dadosUsuario['DT_NASCIMENTO']}, portador do CPF n° {$dadosUsuario['NU_CPF']}, da carteira de identidade n° {$dadosUsuario['NU_IDENTIDADE']}, expedida em {$dadosUsuario['DT_EMISSAO_DOCUMENTACAO']}, por {$dadosUsuario['CO_ORGAO_EMISSOR']}/{$dadosUsuario['SG_UF_EMISSAO_DOC']}, morador no endereço {$dadosUsuario['DS_ENDERECO']}, bairro {$dadosUsuario['DS_BAIRRO_ENDERECO']} - {$dadosUsuario['MUNICIPIO']['NO_MUNICIPIO']}/{$dadosUsuario['MUNICIPIO']['SG_UF']}, CEP {$dadosUsuario['NU_CEP']}, telefones residencial {$dadosUsuario['DS_TELEFONE_USUARIO']} e comercial {$dadosUsuario['DS_CELULAR_USUARIO']}, e-mail {$dadosUsuario['DS_EMAIL_USUARIO']},
            confirmo estar em condições de participar do Programa desempenhando a função de {$papel}.
            </p>

            <p>
            Além disso, comprometo-me a:
            </p>

            <p align='justify' style='margin-left: 30px;'>
            - fornecer os documentos comprobatórios dos requisitos para a inscrição e permanência no Programa sempre que solicitado;<br/>
            - dedicar-me com afinco às atividades do Curso de Formação para Conselheiros Escolares, conforme competências específicas definidas nos normativos do Programa; e<br/>
            - não acumular mais de uma bolsa de estudo e pesquisa regida pela Lei 11.273/2006.<br/>
            </p>

            <p align='justify'>
            Estou ciente de que, para fazer jus ao recebimento da bolsa de estudo e pesquisa destinada ao {$papel}, devo realizar com dedicação e eficiência todas as atribuições previstas, entre as quais se destacam:
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
                $aceite = "concordar com o Termo de Compromisso e por <strong>NÃO</strong> receber bolsa.";
                break;
            case Fnde_Sice_Model_TermoCompromisso::CO_ACAO_VIEW:
                $aceite = "não concordar com o Termo de Compromisso.";
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