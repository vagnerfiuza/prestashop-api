<?php

/** MODULO CRIADO SUPORTE MOIP
 * @author Vagner Fiuza Vieira
 * @copyright MoIP Labs
 * @email suporte@moip.com.br
 * @version I.API v1.0 MoIP Labs
 * */

class MoIP extends PaymentModule {
    const INSTALL_SQL_FILE = 'install.sql';

    private $_html = '';
    private $_postErrors = array();
    public $currencies;
    public $banners = array(
        "imgs/logo_moip.gif" => "imgs/logo_moip.gif",
        "imgs/formas_pagamento.png" => "imgs/formas_pagamento.png",
        "imgs/formas_pagamento_6x.png" => "imgs/formas_pagamento_6x.png",
        "imgs/formas_pagamento_bancos.png" => "imgs/formas_pagamento_bancos.png",
        "imgs/formas_pagamento_bancos_boleto.png" => "imgs/formas_pagamento_bancos_boleto.png",
        "imgs/formas_pagamento_boleto.png" => "imgs/formas_pagamento_boleto.png",
        "imgs/formas_pagamento_cartoes.png" => "imgs/formas_pagamento_cartoes.png",
        "imgs/formas_pagamento_cartoes_bancos.png" => "imgs/formas_pagamento_cartoes_bancos.png",
        "imgs/formas_pagamento_cartoes_boleto.png" => "imgs/formas_pagamento_cartoes_boleto.png",
        'imgs/buttons/bt_pagar_c01_e04.png' => 'imgs/buttons/bt_pagar_c01_e04.png',
        'imgs/buttons/bt_pagar_c02_e04.png' => 'imgs/buttons/bt_pagar_c02_e04.png',
        'imgs/buttons/bt_pagar_c03_e04.png' => 'imgs/buttons/bt_pagar_c03_e04.png',
        'imgs/buttons/bt_pagar_c04_e04.png' => 'imgs/buttons/bt_pagar_c04_e04.png',
        'imgs/buttons/bt_pagar_c05_e04.png' => 'imgs/buttons/bt_pagar_c05_e04.png',
        'imgs/buttons/bt_pagar_c06_e04.png' => 'imgs/buttons/bt_pagar_c06_e04.png',
        'imgs/buttons/bt_pagar_c07_e04.png' => 'imgs/buttons/bt_pagar_c07_e04.png',
        'imgs/buttons/bt_pagar_c08_e04.png' => 'imgs/buttons/bt_pagar_c08_e04.png',
        'imgs/buttons/bt_pagar_c09_e04.png' => 'imgs/buttons/bt_pagar_c09_e04.png'
    );
    public $ambiente_moip = array(
        "Produção" => "https://www.moip.com.br",
        "SandBox" => "https://desenvolvedor.moip.com.br/sandbox"
    );
    public $aceitar_parcelamento = array(
        "Aceitar" => true,
        "Não aceitar " => false
    );

    public $comissionamento = array(
        "Ativar" => true,
        "Desativar" => false
    );

    public $pagamento_direto = array(
        "Ativar" => true,
        "Desativar" => false
    );

    public $pd_boleto = array(
        "sim" => 'Sim',
        "nao" => 'Não'
    );
    public $comissionamento_tipo_1 = array(
        "fixo" => 'Fixo',
        "percentual" => 'Percentual'
    );
    public $comissionamento_tipo_2 = array(
        "fixo" => 'Fixo',
        "percentual" => 'Percentual'
    );
    public $comissionamento_tipo_3 = array(
        "fixo" => 'Fixo',
        "percentual" => 'Percentual'
    );
    public $pd_debito = array(
        "sim" => 'Sim',
        "nao" => 'Não'
    );
    public $pd_credito = array(
        "sim" => 'Sim',
        "nao" => 'Não'
    );

    public function __construct() {

        $this->name = 'MoIP';
        $this->tab = 'payments_gateways';
        $this->version = ' I.API 1.0 | MoIP Labs';

        $this->currencies = true;
        $this->currencies_mode = 'radio';

        parent::__construct();

        $this->page = basename(__file__, '.php');
        $this->displayName = $this->l('MoIP');
        $this->description = $this->l('Aceitar pagamentos via MoIP');
        $this->confirmUninstall = $this->l('Tem certeza de que deseja desinstalar o Módulo MoIP Pagamentos?');
        $this->textButton = $this->l('Efetuar Pagamento');
    }

    public function install() {
        // SQL Table
        if (!file_exists(dirname(__FILE__) . '/' . self::INSTALL_SQL_FILE))
            die('lol');
        elseif (!$sql = file_get_contents(dirname(__FILE__) . '/' . self::INSTALL_SQL_FILE))
            die('lal');
        $sql = str_replace('PREFIX_', _DB_PREFIX_, $sql);
        $sql = preg_split("/;\s*[\r\n]+/", $sql);
        foreach ($sql as $query)
            if ($query AND sizeof($query) AND !Db::getInstance()->Execute(trim($query)))
                return false;
        // SQL Table


        $key_nasp_new = rand(000000, 999999);

        if (!Configuration::get('MoIP_STATUS_1'))
            $this->create_states();

        if
            (
                !parent::install()
                OR !Configuration::updateValue('MoIP_BUSINESS', 'seu_login_moip')
                OR !Configuration::updateValue('ambiente_moip', 'https://www.moip.com.br')
                OR !Configuration::updateValue('LAYOUT', 'default')
                OR !Configuration::updateValue('MoIP_BANNER', 'imgs/logo_moip.gif')
                OR !Configuration::updateValue('MoIP_ID_TRANSACAO_PREFIX', 'Seu nome fantasia')
                OR !Configuration::updateValue('KEY_NASP', $key_nasp_new)
                OR !Configuration::updateValue('ACEITAR_PARCELAMENTO', true)
                OR !Configuration::updateValue('PAGAMENTO_DIRETO', false)
                OR !Configuration::updateValue('PD_BOLETO', 'nao')
                OR !Configuration::updateValue('PD_DEBITO', 'nao')
                OR !Configuration::updateValue('PD_CREDITO', 'nao')
                OR !Configuration::updateValue('PARCELAMENTO_DE_1', '1')
                OR !Configuration::updateValue('PARCELAMENTO_DE_2', '')
                OR !Configuration::updateValue('PARCELAMENTO_DE_3', '')
                OR !Configuration::updateValue('PARCELAMENTO_ATE_1', '12')
                OR !Configuration::updateValue('PARCELAMENTO_ATE_2', '')
                OR !Configuration::updateValue('PARCELAMENTO_ATE_3', '')
                OR !Configuration::updateValue('PARCELAMENTO_JUROS_1', '1.99')
                OR !Configuration::updateValue('PARCELAMENTO_JUROS_2', '')
                OR !Configuration::updateValue('PARCELAMENTO_JUROS_3', '')
                OR !Configuration::updateValue('COMISSIONAMENTO', false)
                OR !Configuration::updateValue('COMISSIONAMENTO_LOGIN_1', '')
                OR !Configuration::updateValue('COMISSIONAMENTO_LOGIN_2', '')
                OR !Configuration::updateValue('COMISSIONAMENTO_LOGIN_3', '')
                OR !Configuration::updateValue('COMISSIONAMENTO_TIPO_1', '')
                OR !Configuration::updateValue('COMISSIONAMENTO_TIPO_2', '')
                OR !Configuration::updateValue('COMISSIONAMENTO_TIPO_3', '')
                OR !Configuration::updateValue('COMISSIONAMENTO_VALOR_1', '')
                OR !Configuration::updateValue('COMISSIONAMENTO_VALOR_2', '')
                OR !Configuration::updateValue('COMISSIONAMENTO_VALOR_3', '')
                OR !$this->registerHook('payment')
                OR !$this->registerHook('paymentReturn')
                OR !$this->registerHook('home')
            )
            return false;
        return true;
    }

    public function create_states() {

        $this->order_state = array(
            array('c9fecd', '11110', 'MoIP - Autorizado', 'payment'),
            array('ffffff', '00100', 'MoIP - Iniciado', ''),
            array('fcffcf', '00100', 'MoIP - Boleto Impresso', ''),
            array('c9fecd', '00100', 'MoIP - Concluido', 'bankwire'),
            array('fec9c9', '11110', 'MoIP - Cancelado', 'order_canceled'),
            array('fcffcf', '00100', 'MoIP - Em Analise', ''),
            array('ffe0bb', '11100', 'MoIP - Estornado', 'refund'),
            array('d6d6d6', '00100', 'MoIP - Em Aberto', '')
        );

        /** OBTENDO UMA LISTA DOS IDIOMAS  * */
        $languages = Db::getInstance()->ExecuteS('
            SELECT `id_lang`, `iso_code`
            FROM `' . _DB_PREFIX_ . 'lang`
            ');
        /** /OBTENDO UMA LISTA DOS IDIOMAS  * */
        /** INSTALANDO STATUS MOIP * */
        foreach ($this->order_state as $key => $value) {
            /** CRIANDO OS STATUS NA TABELA order_state * */
            Db::getInstance()->Execute
                ('
                INSERT INTO `' . _DB_PREFIX_ . 'order_state` 
                ( `invoice`, `send_email`, `color`, `unremovable`, `logable`, `delivery`) 
                VALUES
                (' . $value[1][0] . ', ' . $value[1][1] . ', \'#' . $value[0] . '\', ' . $value[1][2] . ', ' . $value[1][3] . ', ' . $value[1][4] . ');
            ');
            /** /CRIANDO OS STATUS NA TABELA order_state * */
            $this->figura = mysql_insert_id();

            foreach ($languages as $language_atual) {
                /** CRIANDO AS DESCRI��ES DOS STATUS NA TABELA order_state_lang  * */
                Db::getInstance()->Execute
                    ('
                    INSERT INTO `' . _DB_PREFIX_ . 'order_state_lang` 
                    (`id_order_state`, `id_lang`, `name`, `template`)
                    VALUES
                    (' . $this->figura . ', ' . $language_atual['id_lang'] . ', \'' . $value[2] . '\', \'' . $value[3] . '\');
                ');
                /** /CRIANDO AS DESCRI��ES DOS STATUS NA TABELA order_state_lang  * */
            }


            /** COPIANDO O ICONE ATUAL * */
            $file = (dirname(__file__) . "/icons/$key.gif");
            $newfile = (dirname(dirname(dirname(__file__))) . "/img/os/$this->figura.gif");
            if (!copy($file, $newfile)) {
                return false;
            }
            /** /COPIANDO O ICONE ATUAL * */
            /** GRAVA AS CONFIGURA��ES  * */
            Configuration::updateValue("MoIP_STATUS_$key", $this->figura);
        }

        return true;
    }

    public function uninstall() {

        if (
            !Configuration::deleteByName('MoIP_BUSINESS')
            OR !Configuration::deleteByName('ambiente_moip')
            OR !Configuration::deleteByName('MoIP_BANNER')
            OR !Configuration::deleteByName('LAYOUT')
            OR !Configuration::deleteByName('MoIP_ID_TRANSACAO_PREFIX')
            OR !Configuration::deleteByName('ACEITAR_PARCELAMENTO')
            OR !Configuration::deleteByName('KEY_NASP')
            OR !Configuration::deleteByName('PAGAMENTO_DIRETO')
            OR !Configuration::deleteByName('PD_BOLETO')
            OR !Configuration::deleteByName('PD_DEBITO')
            OR !Configuration::deleteByName('PD_CREDITO')
            OR !Configuration::deleteByName('PARCELAMENTO_DE_1')
            OR !Configuration::deleteByName('PARCELAMENTO_DE_2')
            OR !Configuration::deleteByName('PARCELAMENTO_DE_3')
            OR !Configuration::deleteByName('PARCELAMENTO_ATE_1')
            OR !Configuration::deleteByName('PARCELAMENTO_ATE_2')
            OR !Configuration::deleteByName('PARCELAMENTO_ATE_3')
            OR !Configuration::deleteByName('PARCELAMENTO_JUROS_1')
            OR !Configuration::deleteByName('PARCELAMENTO_JUROS_2')
            OR !Configuration::deleteByName('PARCELAMENTO_JUROS_3')
            OR !Configuration::deleteByName('COMISSIONAMENTO')
            OR !Configuration::deleteByName('COMISSIONAMENTO_LOGIN_1')
            OR !Configuration::deleteByName('COMISSIONAMENTO_LOGIN_2')
            OR !Configuration::deleteByName('COMISSIONAMENTO_LOGIN_3')
            OR !Configuration::deleteByName('COMISSIONAMENTO_TIPO_1')
            OR !Configuration::deleteByName('COMISSIONAMENTO_TIPO_2')
            OR !Configuration::deleteByName('COMISSIONAMENTO_TIPO_3')
            OR !Configuration::deleteByName('COMISSIONAMENTO_VALOR_1')
            OR !Configuration::deleteByName('COMISSIONAMENTO_VALOR_2')
            OR !Configuration::deleteByName('COMISSIONAMENTO_VALOR_3')
            OR !parent::uninstall())
            return false;
        return true;
    }

    public function getContent() {

        $this->_html = '<h2>MoIP</h2>';
        if (isset($_POST['submitMoIP'])) {
            if (empty($_POST['business']))
                $this->_postErrors[] = $this->l('Digite seu Login cadastrado com o MoIP');

            //			elseif (!Validate::isEmail($_POST['business'])) 
            //			$this->_postErrors[] = $this->l('Digite um e-mail válido.');

            if (!sizeof($this->_postErrors)) {
                Configuration::updateValue('MoIP_BUSINESS', $_POST['business']);
                Configuration::updateValue('ambiente_moip', $_POST['ambiente_moip']);
                Configuration::updateValue('LAYOUT', $_POST['layout']);
                Configuration::updateValue('MoIP_ID_TRANSACAO_PREFIX', $_POST['prefix_id_transacao']);
                Configuration::updateValue('KEY_NASP', $_POST['key_nasp']);
                Configuration::updateValue('PAGAMENTO_DIRETO', $_POST['pagamento_direto']);
                Configuration::updateValue('PD_BOLETO', $_POST['pd_boleto']);
                Configuration::updateValue('PD_DEBITO', $_POST['pd_debito']);
                Configuration::updateValue('PD_CREDITO', $_POST['pd_credito']);

                $this->displayConf();
            }
            else
                $this->displayErrors();
        }
        elseif (isset($_POST['submitMoIP_Comissionamento'])) {
            Configuration::updateValue('COMISSIONAMENTO', $_POST['comissionamento']);
            Configuration::updateValue('COMISSIONAMENTO_LOGIN_1', $_POST['comissionamento_login_1']);
            Configuration::updateValue('COMISSIONAMENTO_LOGIN_2', $_POST['comissionamento_login_2']);
            Configuration::updateValue('COMISSIONAMENTO_LOGIN_3', $_POST['comissionamento_login_3']);
            Configuration::updateValue('COMISSIONAMENTO_TIPO_1', $_POST['comissionamento_tipo_1']);
            Configuration::updateValue('COMISSIONAMENTO_TIPO_2', $_POST['comissionamento_tipo_2']);
            Configuration::updateValue('COMISSIONAMENTO_TIPO_3', $_POST['comissionamento_tipo_3']);
            Configuration::updateValue('COMISSIONAMENTO_VALOR_1', $_POST['comissionamento_valor_1']);
            Configuration::updateValue('COMISSIONAMENTO_VALOR_2', $_POST['comissionamento_valor_2']);
            Configuration::updateValue('COMISSIONAMENTO_VALOR_3', $_POST['comissionamento_valor_3']);
        }
        elseif (isset($_POST['submitMoIP_Parcelamento'])) {
            Configuration::updateValue('ACEITAR_PARCELAMENTO', $_POST['aceitar_parcelamento']);
            Configuration::updateValue('PARCELAMENTO_DE_1', $_POST['parcelamento_de_1']);
            Configuration::updateValue('PARCELAMENTO_ATE_1', $_POST['parcelamento_ate_1']);
            Configuration::updateValue('PARCELAMENTO_JUROS_1', $_POST['parcelamento_juros_1']);
            Configuration::updateValue('PARCELAMENTO_DE_2', $_POST['parcelamento_de_2']);
            Configuration::updateValue('PARCELAMENTO_ATE_2', $_POST['parcelamento_ate_2']);
            Configuration::updateValue('PARCELAMENTO_JUROS_2', $_POST['parcelamento_juros_2']);
            Configuration::updateValue('PARCELAMENTO_DE_3', $_POST['parcelamento_de_3']);
            Configuration::updateValue('PARCELAMENTO_ATE_3', $_POST['parcelamento_ate_3']);
            Configuration::updateValue('PARCELAMENTO_JUROS_3', $_POST['parcelamento_juros_3']);
            $this->displayConf();
        } elseif (isset($_POST['submitMoIP_Banner'])) {
            Configuration::updateValue('MoIP_BANNER', $_POST['banner']);
            $this->displayConf();
        }

        $this->displayMoIP();
        $this->displayFormSettingsMoIP();
        return $this->_html;
    }

    public function displayConf() {

        $this->_html .= '
            <div class="conf confirm">
            <img src="../img/admin/ok.gif" alt="' . $this->l('Confirmation') . '" />
            ' . $this->l('Configurações atualizadas') . '
            </div>';
    }

    public function displayErrors() {

        $nbErrors = sizeof($this->_postErrors);
        $this->_html .= '
            <div class="alert error">
            <h3>' . ($nbErrors > 1 ? $this->l('There are') : $this->l('There is')) . ' ' . $nbErrors . ' ' . ($nbErrors > 1 ? $this->l('errors') : $this->l('error')) . '</h3>
            <ol>';
        foreach ($this->_postErrors AS $error)
            $this->_html .= '<li>' . $error . '</li>';
        $this->_html .= '
            </ol>
            </div>';
    }

    public function displayMoIP() {

        $KEY_NASP = Configuration::get('KEY_NASP');

        $this->_html .= '
            <img src="https://www.moip.com.br/imgs/logo_moip.gif" style="float:left; margin-right:15px;" /><b>
            ' . $this->l('Este módulo permite aceitar pagamentos via MoIP.') . '</b><br /><br />
            ' . $this->l('Se o cliente escolher o módulo de pagamento, a conta do MoIP sera automaticamente creditado.') . '<br />
            ' . $this->l('Você precisa configurar com seu login MoIP, para depois usar este módulo.') . '<br /><br />
            ' . $this->l('Será necessário cadastrar a URL de notificação em sua conta MoIP para que o módulo atualize o status de seus pagamentos.') . '<br />
            ' . $this->l('Acesse sua conta MoIP no menu "Meus dados" >> "Preferências" >> "Notificação das transações", e marque a opção "Receber notificação instantânea de transação".') . '<br />
            ' . $this->l('Em "<b>URL de notificação</b>" coloque a seguinte URL: <b>https://'. htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8') . __PS_BASE_URI__ . 'modules/MoIP/validation.php?key=' . htmlentities($KEY_NASP, ENT_COMPAT, 'UTF-8') . '</b><br />') . '
            ' . $this->l('Para utilização do Pagamento Direto é obrigatório obter certificado SSL em seu checkout, caso não utilize esse ferramenta sua URL de notificação poderá conter somente (http://).') . '
            ' . $this->l('<br /><br /><b>Pronto</b>, sua loja está integrada com o MoIP !!!') . '
            <br /><br /><br />';
    }

    public function displayFormSettingsMoIP() {

        $conf = Configuration::getMultiple(array('MoIP_BUSINESS', 'LAYOUT', 'ambiente_moip', 'MoIP_BANNER', 'MoIP_BUTTON', 'MoIP_ID_TRANSACAO_PREFIX', 'KEY_NASP', 'PAGAMENTO_DIRETO', 'PD_BOLETO', 'PD_DEBITO', 'PD_CREDITO','ACEITAR_PARCELAMENTO', 'PARCELAMENTO_DE_1', 'PARCELAMENTO_ATE_1', 'PARCELAMENTO_JUROS_1', 'PARCELAMENTO_DE_2', 'PARCELAMENTO_ATE_2', 'PARCELAMENTO_JUROS_2', 'PARCELAMENTO_DE_3', 'PARCELAMENTO_ATE_3', 'PARCELAMENTO_JUROS_3', 'COMISSIONAMENTO', 'COMISSIONAMENTO_LOGIN_1', 'COMISSIONAMENTO_TIPO_1', 'COMISSIONAMENTO_VALOR_1', 'COMISSIONAMENTO_LOGIN_2', 'COMISSIONAMENTO_LOGIN_2', 'COMISSIONAMENTO_TIPO_2', 'COMISSIONAMENTO_VALOR_2', 'COMISSIONAMENTO_LOGIN_3', 'COMISSIONAMENTO_TIPO_3','COMISSIONAMENTO_VALOR_3'));
        $id_carteira = array_key_exists('business', $_POST) ? $_POST['business'] : (array_key_exists('MoIP_BUSINESS', $conf) ? $conf['MoIP_BUSINESS'] : '');
        $layout_moip = array_key_exists('layout', $_POST) ? $_POST['layout'] : (array_key_exists('LAYOUT', $conf) ? $conf['LAYOUT'] : '');
        $ambiente_moip = array_key_exists('ambiente_moip', $_POST) ? $_POST['ambiente_moip'] : (array_key_exists('ambiente_moip', $conf) ? $conf['ambiente_moip'] : '');
        $banner = array_key_exists('banner', $_POST) ? $_POST['banner'] : (array_key_exists('MoIP_BANNER', $conf) ? $conf['MoIP_BANNER'] : '');
        $button = array_key_exists('button', $_POST) ? $_POST['button'] : (array_key_exists('MoIP_BUTTON', $conf) ? $conf['MoIP_BUTTON'] : '');
        $id_transacao_prefix = array_key_exists('prefix_id_transacao', $_POST) ? $_POST['prefix_id_transacao'] : (array_key_exists('MoIP_ID_TRANSACAO_PREFIX', $conf) ? $conf['MoIP_ID_TRANSACAO_PREFIX'] : '');
        $key_nasp = array_key_exists('key_nasp', $_POST) ? $_POST['key_nasp'] : (array_key_exists('KEY_NASP', $conf) ? $conf['KEY_NASP'] : '');
        $pagamento_direto = array_key_exists('pagamento_direto', $_POST) ? $_POST['pagamento_direto'] : (array_key_exists('PAGAMENTO_DIRETO', $conf) ? $conf['PAGAMENTO_DIRETO'] : '');
        $pd_boleto = array_key_exists('pd_boleto', $_POST) ? $_POST['pd_boleto'] : (array_key_exists('PD_BOLETO', $conf) ? $conf['PD_BOLETO'] : '');
        $pd_debito = array_key_exists('pd_debito', $_POST) ? $_POST['pd_debito'] : (array_key_exists('PD_DEBITO', $conf) ? $conf['PD_DEBITO'] : '');
        $pd_credito = array_key_exists('pd_credito', $_POST) ? $_POST['pd_credito'] : (array_key_exists('PD_CREDITO', $conf) ? $conf['PD_CREDITO'] : '');

        $aceitar_parcelamento = array_key_exists('aceitar_parcelamento', $_POST) ? $_POST['aceitar_parcelamento'] : (array_key_exists('ACEITAR_PARCELAMENTO', $conf) ? $conf['ACEITAR_PARCELAMENTO'] : '');
        $parcelamento_de_1 = array_key_exists('parcelamento_de_1', $_POST) ? $_POST['parcelamento_de_1'] : (array_key_exists('PARCELAMENTO_DE_1', $conf) ? $conf['PARCELAMENTO_DE_1'] : '');
        $parcelamento_ate_1 = array_key_exists('parcelamento_ate_1', $_POST) ? $_POST['parcelamento_ate_1'] : (array_key_exists('PARCELAMENTO_ATE_1', $conf) ? $conf['PARCELAMENTO_ATE_1'] : '');
        $parcelamento_juros_1 = array_key_exists('parcelamento_juros_1', $_POST) ? $_POST['parcelamento_juros_1'] : (array_key_exists('PARCELAMENTO_JUROS_1', $conf) ? $conf['PARCELAMENTO_JUROS_1'] : '');

        $parcelamento_de_2 = array_key_exists('parcelamento_de_2', $_POST) ? $_POST['parcelamento_de_2'] : (array_key_exists('PARCELAMENTO_DE_2', $conf) ? $conf['PARCELAMENTO_DE_2'] : '');
        $parcelamento_ate_2 = array_key_exists('parcelamento_ate_2', $_POST) ? $_POST['parcelamento_ate_2'] : (array_key_exists('PARCELAMENTO_ATE_2', $conf) ? $conf['PARCELAMENTO_ATE_2'] : '');
        $parcelamento_juros_2 = array_key_exists('parcelamento_juros_2', $_POST) ? $_POST['parcelamento_juros_2'] : (array_key_exists('PARCELAMENTO_JUROS_2', $conf) ? $conf['PARCELAMENTO_JUROS_2'] : '');

        $parcelamento_de_3 = array_key_exists('parcelamento_de_3', $_POST) ? $_POST['parcelamento_de_3'] : (array_key_exists('PARCELAMENTO_DE_3', $conf) ? $conf['PARCELAMENTO_DE_3'] : '');
        $parcelamento_ate_3 = array_key_exists('parcelamento_ate_3', $_POST) ? $_POST['parcelamento_ate_3'] : (array_key_exists('PARCELAMENTO_ATE_3', $conf) ? $conf['PARCELAMENTO_ATE_3'] : '');
        $parcelamento_juros_3 = array_key_exists('parcelamento_juros_3', $_POST) ? $_POST['parcelamento_juros_3'] : (array_key_exists('PARCELAMENTO_JUROS_3', $conf) ? $conf['PARCELAMENTO_JUROS_3'] : '');

        $comissionamento = array_key_exists('comissionamento', $_POST) ? $_POST['comissionamento'] : (array_key_exists('COMISSIONAMENTO', $conf) ? $conf['COMISSIONAMENTO'] : '');

        $comissionamento_login_1 = array_key_exists('comissionamento_login_1', $_POST) ? $_POST['comissionamento_login_1'] : (array_key_exists('COMISSIONAMENTO_LOGIN_1', $conf) ? $conf['COMISSIONAMENTO_LOGIN_1'] : '');
        $comissionamento_tipo_1 = array_key_exists('comissionamento_tipo_1', $_POST) ? $_POST['comissionamento_tipo_1'] : (array_key_exists('COMISSIONAMENTO_TIPO_1', $conf) ? $conf['COMISSIONAMENTO_TIPO_1'] : '');
        $comissionamento_valor_1 = array_key_exists('comissionamento_valor_1', $_POST) ? $_POST['comissionamento_valor_1'] : (array_key_exists('COMISSIONAMENTO_VALOR_1', $conf) ? $conf['COMISSIONAMENTO_VALOR_1'] : '');

        $comissionamento_login_2 = array_key_exists('comissionamento_login_2', $_POST) ? $_POST['comissionamento_login_2'] : (array_key_exists('COMISSIONAMENTO_LOGIN_2', $conf) ? $conf['COMISSIONAMENTO_LOGIN_2'] : '');
        $comissionamento_tipo_2 = array_key_exists('comissionamento_tipo_2', $_POST) ? $_POST['comissionamento_tipo_2'] : (array_key_exists('COMISSIONAMENTO_TIPO_2', $conf) ? $conf['COMISSIONAMENTO_TIPO_2'] : '');
        $comissionamento_valor_2 = array_key_exists('comissionamento_valor_2', $_POST) ? $_POST['comissionamento_valor_2'] : (array_key_exists('COMISSIONAMENTO_VALOR_2', $conf) ? $conf['COMISSIONAMENTO_VALOR_2'] : '');

        $comissionamento_login_3 = array_key_exists('comissionamento_login_3', $_POST) ? $_POST['comissionamento_login_3'] : (array_key_exists('COMISSIONAMENTO_LOGIN_3', $conf) ? $conf['COMISSIONAMENTO_LOGIN_3'] : '');
        $comissionamento_tipo_3 = array_key_exists('comissionamento_tipo_3', $_POST) ? $_POST['comissionamento_tipo_3'] : (array_key_exists('COMISSIONAMENTO_TIPO_3', $conf) ? $conf['COMISSIONAMENTO_TIPO_3'] : '');
        $comissionamento_valor_3 = array_key_exists('comissionamento_valor_3', $_POST) ? $_POST['comissionamento_valor_3'] : (array_key_exists('COMISSIONAMENTO_VALOR_3', $conf) ? $conf['COMISSIONAMENTO_VALOR_3'] : '');

        /** CONFIGURAÇÕES **/
        $this->_html .= '
            <form action="' . $_SERVER['REQUEST_URI'] . '" method="post">
            <fieldset>
            <legend><img src="../img/admin/contact.gif" />' . $this->l('Configurações') . '</legend>
            <label>' . $this->l('Login MoIP') . ':</label>
            <div class="margin-form"><input type="text" size="33" name="business" value="' . htmlentities($id_carteira, ENT_COMPAT, 'UTF-8') . '" /></div><br>
            <label>' . $this->l('Layout') . ':</label>
            <div class="margin-form"><input type="text" size="33" name="layout" value="' . htmlentities($layout_moip, ENT_COMPAT, 'UTF-8') . '" /></div><br>
            <label>' . $this->l('Prefixo ID Proprio') . ':</label>
            <div class="margin-form"><input type="text" size="22" maxlength="22" name="prefix_id_transacao" value="' . htmlentities($id_transacao_prefix, ENT_COMPAT, 'UTF-8') . '" /></div><br>
            <label>' . $this->l('Chave NASP') . ':</label>
            <div class="margin-form"><input type="text" size="6" maxlength="6" name="key_nasp" value="' . htmlentities($key_nasp, ENT_COMPAT, 'UTF-8') . '" /></div><br>

            <label>' . $this->l('Ambiente') . ':</label>
            <div class="margin-form"><select name="ambiente_moip">';
        foreach ($this->ambiente_moip as $id => $value) {
            if ($ambiente_moip == $value) {
                $check = 'selected';
            } else {
                $check = '';
            }
            $this->_html .= '
                <option value="' . $value . '" ' . $check . '>' . $id . '</option>';
        }
        $this->_html .= '
            </select></div><br>';


        $this->_html .= '
            <label>' . $this->l('Pagamento Direto') . ':</label>
            <div class="margin-form"><select name="pagamento_direto">';
        foreach ($this->pagamento_direto as $id => $value) {
            if ($pagamento_direto == $value) {
                $check = 'selected';
            } else {
                $check = '';
            }
            $this->_html .= '
                <option value="' . $value . '" ' . $check . '>' . $id . '</option>';
        }
        $this->_html .= '
            </select></div>';


        $this->_html .= '
            <label>' . $this->l('Boleto') . ':</label>';
        $this->_html .= '			<div  class="margin-form">';

        foreach ($this->pd_boleto as $id => $value) {
            if ($pd_boleto == $id) {
                $check = 'checked="checked"';
            } else {
                $check = '';
            }

            $this->_html .= '
                <input type="radio" name="pd_boleto" value="' . $id . '" ' . $check . ' >' . $value . '
                ';
        }
        $this->_html .= '        </div>';

        $this->_html .= '
            <label>' . $this->l('Débito Online') . ':</label>';
        $this->_html .= '			<div  class="margin-form">';

        foreach ($this->pd_debito as $id => $value) {
            if ($pd_debito == $id) {
                $check = 'checked="checked"';
            } else {
                $check = '';
            }
            $this->_html .= '
                <input type="radio" name="pd_debito" value="' . $id . '" ' . $check . ' >' . $value . '
                ';
        }
        $this->_html .= '        </div>';
        $this->_html .= '
            <label>' . $this->l('Cartão de crédito') . ':</label>';
        $this->_html .= '			<div  class="margin-form">';

        foreach ($this->pd_credito as $id => $value) {
            if ($pd_credito == $id) {
                $check = 'checked="checked"';
            } else {
                $check = '';
            }

            $this->_html .= '
                <input type="radio" name="pd_credito" value="' . $id . '" ' . $check . ' >' . $value . '
                ';
        }
        $this->_html .= '        </div>';


        $this->_html .= '
            <br /><center><input type="submit" name="submitMoIP" value="' . $this->l('Salvar Configurações') . '" class="button" /></center>
            </fieldset>
            </form>';



        /** PARCELAMENTO * */
        $this->_html .= '<br>
            <form action="' . $_SERVER['REQUEST_URI'] . '" method="post">
            <fieldset>
            <legend><img src="../img/admin/coupon.gif" />' . $this->l('Configurações de Parcelamentos para Pagamento Direto') . '</legend>';

        $this->_html .= '<label>' . $this->l('Parcelamento') . ':</label>
            <div class="margin-form"><select name="aceitar_parcelamento">';

        foreach ($this->aceitar_parcelamento as $id => $value) {
            if ($aceitar_parcelamento == $value) {
                $check = 'selected';
            } else {
                $check = '';
            }

            $this->_html .= '
                <option value="' . $value . '" ' . $check . '>' . $id . '</option>';
        }


        $this->_html .= '
            </select>
            </div><br><br>
            <fieldset>
            <legend><img src="../img/admin/duplicate.gif" />' . $this->l('1° Parcelamento') . '</legend>
            <label>' . $this->l('De') . ':</label>
            <div class="margin-form"><input type="text" size="2" maxlength="2" name="parcelamento_de_1" value="' . htmlentities($parcelamento_de_1, ENT_COMPAT, 'UTF-8') . '" /></div><br>
            <label>' . $this->l('Até') . ':</label>
            <div class="margin-form"><input type="text" size="2" maxlength="2" name="parcelamento_ate_1" value="' . htmlentities($parcelamento_ate_1, ENT_COMPAT, 'UTF-8') . '" /></div><br>
            <label>' . $this->l('Taxa de Juros') . ':</label>
            <div class="margin-form"><input type="text" size="5" name="parcelamento_juros_1" value="' . htmlentities($parcelamento_juros_1, ENT_COMPAT, 'UTF-8') . '" />%</div><br>
            </fieldset>
            <br>
            <fieldset>
            <legend><img src="../img/admin/duplicate.gif" />' . $this->l('2° Parcelamento') . '</legend>

            <label>' . $this->l('De') . ':</label>
            <div class="margin-form"><input type="text" size="2" maxlength="2" name="parcelamento_de_2" value="' . htmlentities($parcelamento_de_2, ENT_COMPAT, 'UTF-8') . '" /></div><br>
            <label>' . $this->l('Até') . ':</label>
            <div class="margin-form"><input type="text" size="2" maxlength="2" name="parcelamento_ate_2" value="' . htmlentities($parcelamento_ate_2, ENT_COMPAT, 'UTF-8') . '" /></div><br>
            <label>' . $this->l('Taxa de Juros') . ':</label>
            <div class="margin-form"><input type="text" size="5" name="parcelamento_juros_2" value="' . htmlentities($parcelamento_juros_2, ENT_COMPAT, 'UTF-8') . '" />%</div><br>
            </fieldset>
            <br>
            <fieldset>
            <legend><img src="../img/admin/duplicate.gif" />' . $this->l('3° Parcelamento') . '</legend>
            <label>' . $this->l('De') . ':</label>
            <div class="margin-form"><input type="text" size="2" maxlength="2" name="parcelamento_de_3" value="' . htmlentities($parcelamento_de_3, ENT_COMPAT, 'UTF-8') . '" /></div><br>
            <label>' . $this->l('Até') . ':</label>
            <div class="margin-form"><input type="text" size="2" maxlength="2" name="parcelamento_ate_3" value="' . htmlentities($parcelamento_ate_3, ENT_COMPAT, 'UTF-8') . '" /></div><br>
            <label>' . $this->l('Taxa de Juros') . ':</label>
            <div class="margin-form"><input type="text" size="5" name="parcelamento_juros_3" value="' . htmlentities($parcelamento_juros_3, ENT_COMPAT, 'UTF-8') . '" />%</div><br>
            </fieldset>
            ';

        $this->_html .= '<br /><center><input type="submit" name="submitMoIP_Parcelamento" value="' . $this->l('Salvar Parcelamento') . '"
            class="button" />
                </center>
                </fieldset>
                </form>';


        /** COMISSIONAMENTO * */
        $this->_html .= '<br>
            <form action="' . $_SERVER['REQUEST_URI'] . '" method="post">
            <fieldset>
            <legend><img src="../img/admin/coupon.gif" />' . $this->l('Configurações de Comissionamento') . '</legend>';

        $this->_html .= '<label>' . $this->l('Comissionamento') . ':</label>
            <div class="margin-form"><select name="comissionamento">';

        foreach ($this->comissionamento as $id => $value) {
            if ($comissionamento == $value) {
                $check = 'selected';
            } else {
                $check = '';
            }
            $this->_html .= '
                <option value="' . $value . '" ' . $check . '>' . $id . '</option>';
        }

        $this->_html .= '
            </select>
            </div><br><br>';

        $this->_html .= '
            <fieldset>
            <legend><img src="../img/admin/duplicate.gif" />' . $this->l('1° Comissionado') . '</legend>
            <label>' . $this->l('Login') . ':</label>
            <div class="margin-form"><input type="text" size="33" maxlength="33" name="comissionamento_login_1" value="' . htmlentities($comissionamento_login_1, ENT_COMPAT, 'UTF-8') . '" /></div><br>
            <label>' . $this->l('Tipo') . ':</label>';
        $this->_html .= '<div class="margin-form"><select name="comissionamento_tipo_1">';
        foreach ($this->comissionamento_tipo_1 as $id => $value) {
            if ($comissionamento_tipo_1 == $id) {
                $check = 'selected';
            } else {
                $check = '';
            }
            $this->_html .= '
                <option value="' . $value . '" ' . $check . '>' . $id . '</option>';
        }
    $this->_html .= '</select><br>
        </div><br>
        <label>' . $this->l('Valor') . ':</label>
        <div class="margin-form"><input type="text" size="5" name="comissionamento_valor_1" value="' . htmlentities($comissionamento_valor_1, ENT_COMPAT, 'UTF-8') . '" /></div><br>
        </fieldset>
        <br>';

        $this->_html .= '
            <fieldset>
            <legend><img src="../img/admin/duplicate.gif" />' . $this->l('2° Comissionado') . '</legend>
            <label>' . $this->l('Login') . ':</label>
            <div class="margin-form"><input type="text" size="33" maxlength="33" name="comissionamento_login_2" value="' . htmlentities($comissionamento_login_2, ENT_COMPAT, 'UTF-8') . '" /></div><br>
            <label>' . $this->l('Tipo') . ':</label>';
        $this->_html .= '<div class="margin-form"><select name="comissionamento_tipo_2">';
        foreach ($this->comissionamento_tipo_2 as $id => $value) {
            if ($comissionamento_tipo_2 == $id) {
                $check = 'selected';
            } else {
                $check = '';
            }
            $this->_html .= '
                <option value="' . $value . '" ' . $check . '>' . $id . '</option>';
        }
    $this->_html .= '</select><br>
        </div><br>
        <label>' . $this->l('Valor') . ':</label>
        <div class="margin-form"><input type="text" size="5" name="comissionamento_valor_2" value="' . htmlentities($comissionamento_valor_2, ENT_COMPAT, 'UTF-8') . '" /></div><br>
        </fieldset>
        <br>';

        $this->_html .= '
            <fieldset>
            <legend><img src="../img/admin/duplicate.gif" />' . $this->l('3° Comissionado') . '</legend>
            <label>' . $this->l('Login') . ':</label>
            <div class="margin-form"><input type="text" size="33" maxlength="33" name="comissionamento_login_3" value="' . htmlentities($comissionamento_login_3, ENT_COMPAT, 'UTF-8') . '" /></div><br>
            <label>' . $this->l('Tipo') . ':</label>';
        $this->_html .= '<div class="margin-form"><select name="comissionamento_tipo_3">';
        foreach ($this->comissionamento_tipo_3 as $id => $value) {
            if ($comissionamento_tipo_1 == $id) {
                $check = 'selected';
            } else {
                $check = '';
            }
            $this->_html .= '
                <option value="' . $value . '" ' . $check . '>' . $id . '</option>';
        }
    $this->_html .= '</select><br>
        </div><br>
        <label>' . $this->l('Valor') . ':</label>
        <div class="margin-form"><input type="text" size="5" name="comissionamento_valor_3" value="' . htmlentities($comissionamento_valor_3, ENT_COMPAT, 'UTF-8') . '" /></div><br>
        </fieldset>
        <br>';
    $this->_html .= '<br /><center><input type="submit" name="submitMoIP_Comissionamento" value="' . $this->l('Salvar Comissionamento') . '"
        class="button" />
            </center>
            </fieldset>
            </form>';

    /** BANNER * */
    $this->_html .= '<br>
        <form action="' . $_SERVER['REQUEST_URI'] . '" method="post">
        <fieldset>
        <legend><img src="../img/admin/themes.gif" />' . $this->l('Banner') . '</legend>';

    foreach ($this->banners as $id => $value) {
        if ($banner == $id) {
            $check = 'checked="checked"';
        } else {
            $check = '';
        }

        $this->_html .= '
            <div>
            <input type="radio" name="banner" value="' . $id . '" ' . $check . ' >
            <img src="http://www.moip.com.br/' . $value . '" />
            </div>
            <br />';
    }

    $this->_html .= '<br /><center><input type="submit" name="submitMoIP_Banner" value="' . $this->l('Salvar Banner') . '"
        class="button" />
            </center>
            </fieldset>
            </form>';
}

public function execPayment($cart) {
    global $cookie, $smarty, $dados_cliente, $dados_carrinho, $adicionais, $params;
    include("api.php");

    $login_moip = Configuration::get('MoIP_BUSINESS');
    $ambiente_moip = Configuration::get('ambiente_moip');
    $aceitar_parcelamento = Configuration::get('ACEITAR_PARCELAMENTO');
    $comissionamento = Configuration::get('COMISSIONAMENTO');
    $pagamento_direto = Configuration::get('PAGAMENTO_DIRETO');
    $pd_boleto = Configuration::get('PD_BOLETO');
    $pd_debito = Configuration::get('PD_DEBITO');
    $pd_credito = Configuration::get('PD_CREDITO');

    $parcelamento_de_1 = Configuration::get('PARCELAMENTO_DE_1');
    $parcelamento_de_2 = Configuration::get('PARCELAMENTO_DE_2');
    $parcelamento_de_3 = Configuration::get('PARCELAMENTO_DE_3');
    $parcelamento_ate_1 = Configuration::get('PARCELAMENTO_ATE_1');
    $parcelamento_ate_2 = Configuration::get('PARCELAMENTO_ATE_2');
    $parcelamento_ate_3 = Configuration::get('PARCELAMENTO_ATE_3');
    $parcelamento_juros_1 = Configuration::get('PARCELAMENTO_JUROS_1');
    $parcelamento_juros_2 = Configuration::get('PARCELAMENTO_JUROS_2');
    $parcelamento_juros_3 = Configuration::get('PARCELAMENTO_JUROS_3');
    $comissionamento_login_1 = Configuration::get('COMISSIONAMENTO_LOGIN_1');
    $comissionamento_login_2 = Configuration::get('COMISSIONAMENTO_LOGIN_2');
    $comissionamento_login_3 = Configuration::get('COMISSIONAMENTO_LOGIN_3');
    $comissionamento_tipo_1 = Configuration::get('COMISSIONAMENTO_TIPO_1');
    $comissionamento_tipo_2 = Configuration::get('COMISSIONAMENTO_TIPO_2');
    $comissionamento_tipo_3 = Configuration::get('COMISSIONAMENTO_TIPO_3');
    $comissionamento_valor_1 = Configuration::get('COMISSIONAMENTO_VALOR_1');
    $comissionamento_valor_2 = Configuration::get('COMISSIONAMENTO_VALOR_2');
    $comissionamento_valor_3 = Configuration::get('COMISSIONAMENTO_VALOR_3');

    $invoiceAddress = new Address(intval($cart->id_address_invoice));
    $customerPag = new Customer(intval($params['cart']->id_customer));
    $currencies = Currency::getCurrencies();
    $currencies_used = array();

    $conf = Configuration::getMultiple(array('MoIP_BUSINESS', 'MoIP_BANNER'));
    $banner = array_key_exists('banner', $_POST) ? $_POST['banner'] : (array_key_exists('MoIP_BANNER', $conf) ? $conf['MoIP_BANNER'] : '');


    if ($pagamento_direto == true){

        // API Checa Pagamento Direto //

        $base = getAuth($ambiente_moip);
        $auth = base64_encode($base);
        $header[] = "Authorization: Basic " . $auth;

        $ch = curl_init();
        $timeout = 0;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_URL, "$ambiente_moip/ws/alpha/ChecarPagamentoDireto/$login_moip");
        curl_setopt($ch, CURLOPT_USERPWD, $user . ":" . $passwd);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $conteudo = curl_exec($ch);
        $erro = curl_error($ch);
        curl_close($ch);
        // API Checa Pagamento Direto //
        $res = simplexml_load_string($conteudo);
        $resp_xml = $res->Resposta->Status;

        if($pd_boleto == "sim"){
            $resp_pd_boleto = $res->Resposta->BoletoBancario;
        }
        if($pd_debito == "sim"){
            $resp_pd_debito = $res->Resposta->DebitoBancario;
        }
        if($pd_credito == "sim"){
            $resp_pd_credito = $res->Resposta->CartaoCredito;
        }


        $parcelamento = getParcelamento_SON($dados_carrinho['valor'], $parcelamento_de_1, $parcelamento_ate_1, $parcelamento_juros_1, $parcelamento_de_2, $parcelamento_ate_2, $parcelamento_juros_2, $parcelamento_de_3, $parcelamento_ate_3, $parcelamento_juros_3, $login_moip, $ambiente_moip);
    }else{
        $resp_pd_credito = false;
        $resp_pd_boleto = false;
        $resp_pd_debito = false;
    }
    // [X]API Checa Pagamento Direto //
    $currencies = Currency::getCurrencies();
    foreach ($currencies as $key => $currency)
        $smarty->assign(array(
            'currency_default' => new Currency(Configuration::get('PS_CURRENCY_DEFAULT')),
            'currencies' => $currencies_used,
            'imgBtn' => "https://www.moip.com.br/imgs/logo_moip.gif",
            'imgBanner' => "http://www.moip.com.br/" . $this->banners[$banner],
            'currency_default' => new Currency(Configuration::get('PS_CURRENCY_DEFAULT')),
            'currencies' => $currencies_used,
            'total_format' => number_format($cart->getOrderTotal(true, 3), 2, '.', ''),
            'dados_cliente' => $dados_cliente,
            'dados_carrinho' => $dados_carrinho,
            'adicionais' => $adicionais,
            'ambiente_moip' => $ambiente_moip,
            // Dados Carrinho //
            'nome' => $dados_carrinho['nome'],
            'descricao' => $dados_carrinho['descricao'],
            'id_cliente' => $_POST['id_cliente'],
            'total' => $dados_carrinho['valor'],
            'id_carrinho' => $dados_carrinho['id_cart'],
            // Dados Adicionais//
            'id_transacao' => $dados_carrinho['id_transacao'],
            // Pagamento Direto //
            'resp_xml' => $resp_xml,
            'resp_pd_credito' => $resp_pd_credito,
            'resp_pd_boleto' => $resp_pd_boleto,
            'resp_pd_debito' => $resp_pd_debito,
            'parcelamento' => $parcelamento,
            'aceitar_parcelamento' => $aceitar_parcelamento,
            'comissionamento' => $comissionamento,
            'id_Address' => $cart->id_address_invoice,
            'id_cart_payment' => $cart->id,
            'id_cliente' => $_POST['id_cliente'],
            'this_path_ssl' => (Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://') . htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8') . __PS_BASE_URI__ . 'modules/' . $this->name . '/'));

    return $this->display(__file__, 'payment_execution.tpl');

}

public function hookPaymentReturn($status_pd='falha', $CodigoMoIP=false, $id_cliente=false, $msg=false, $erro=false ,$token=false, $tipo=false, $totalApagarCredito=false, $id_cart=false) {
    global $smarty, $cart;

    $MoIP = new MoIP();
    $ambiente_moip = Configuration::get('ambiente_moip');


    $order = new Order($MoIP->currentOrder);
    $DadosOrder = new Order($cart->id);
    $DadosCart = new Cart($DadosOrder->id_cart);
    $currency = new Currency($DadosOrder->id_currency);
    $customer = new Customer(intval($id_cliente));

    if($id_cart != false){
        $cart = new Cart(intval($id_cart));
    }

    $ArrayListaProdutos = $DadosOrder->getProducts();
    $email_comprador = $customer->email;
    $$totalApagarBoleto =

        $smarty->assign(array(
            'totalApagar' => number_format($cart->getOrderTotal(true, 3), 2, '.', ''),
            'totalApagarCredito' => $totalApagarCredito,
            'totalApagarBoleto' => number_format($cart->getOrderTotal(true, 3), 2, '.', '') + 1.00,
            'status' => 'ok',
            'id_order' => $MoIP->currentOrder,
            'secure_key' => $MoIP->secure_key,
            'id_module' => $MoIP->id,
            'status_pd' => $status_pd,
            'CodigoMoIP' => $CodigoMoIP,
            'msg' => $msg,
            'erro' => $erro,
            'token' => $token,
            'tipo' => $tipo,
            'ambiente_moip' => $ambiente_moip,
            'email_comprador' => $email_comprador
        ));

    return $this->display(__file__, 'payment_return.tpl');

}



public function hookPayment($params) {
    global $smarty, $cookie, $orderTotal;


    $address = new Address(intval($params['cart']->id_address_invoice));
    $customer = new Customer(intval($params['cart']->id_customer));
    $business = Configuration::get('MoIP_BUSINESS');
    $ambiente_moip = Configuration::get('ambiente_moip');
    $layout = Configuration::get('LAYOUT');
    $banner = Configuration::get('MoIP_BANNER');
    $header = Configuration::get('PAYPAL_HEADER');
    $id_transacao_prefix = Configuration::get('MoIP_ID_TRANSACAO_PREFIX');
    $currency = $this->getCurrency();



    if (!Validate::isLoadedObject($address) OR !Validate::isLoadedObject($customer) OR !Validate::isLoadedObject($currency))
        return $this->l('MoIP erro: (Endereço referente ao usuario não encontrado.)');

    $products = $params['cart']->getProducts();

    foreach ($products as $key => $product) {
        $products[$key]['name'] = str_replace('"', '\'', $product['name']);
        if (isset($product['attributes']))
            $products[$key]['attributes'] = str_replace('"', '\'', $product['attributes']);
        $products[$key]['name'] = htmlentities(utf8_decode($product['name']));
        $products[$key]['MoIPAmount'] = number_format(Tools::convertPrice($product['price_wt'], $currency), 2, '.', '');
    }


    $smarty->assign(array(
        'address' => $address,
        'country' => new Country(intval($address->id_country)),
        'customer' => $customer,
        'id_carteira' => $business,
        'header' => $header,
        'currency' => $currency,
        // products + discounts - shipping cost
        'amount' => number_format(Tools::convertPrice($params['cart']->getOrderTotal(true, 4), $currency), 2, '', ''),
        // shipping cost + wrapping
        'shipping' => number_format(Tools::convertPrice(($params['cart']->getOrderShippingCost() + $params['cart']->getOrderTotal(true, 6)), $currency), 2, '', ''),
        'discounts' => $params['cart']->getDiscounts(),
        'valor_total' => number_format(Tools::convertPrice($params['cart']->getOrderTotal(true, 4), $currency), 2, '', '') + number_format(Tools::convertPrice(($params['cart']->getOrderShippingCost() + $params['cart']->getOrderTotal(true, 6)), $currency), 2, '', ''),
        'products' => $products,
        'produto' => $product['name'],
        'atributo' => ' [ ' . $product['attributes'] . ' ]',
        'ambiente_moip' => $ambiente_moip,
        'total' => number_format(Tools::convertPrice($params['cart']->getOrderTotal(true, 3), $currency), 2, '.', ''),
        'id_cart' => intval($params['cart']->id),
        'url_retorno' => 'https://' . htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8') . __PS_BASE_URI__ . 'order-confirmation.php?id_cart=' . intval($params['cart']->id) . '&id_module=' . intval($this->id) . '&id_order=' . intval($this->currentOrder) . '&key=' . $customer->secure_key,
        'url_notificacao' => 'https://' . htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8') . __PS_BASE_URI__ . 'modules/MoIP/validation.php',
        'url_retorno_valida' => 'https://' . htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8') . __PS_BASE_URI__ . 'modules/MoIP/validation.php',
        'imgBtn_1' => "https://www.moip.com.br/imgs/logo_moip.gif",
        'imgBtn' => "https://www.moip.com.br/" . $banner,
        'layout' => $layout,
        'id_transacao_prefix' => $id_transacao_prefix,
        'server_name' => $_SERVER[SERVER_NAME],
        'teste' => 'IDlang: ' . $order->id_lang,
        'link_api' => $link_api,
        'params' => $params,
        'this_path' => $this->_path, 'this_path_ssl' => (Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://') . htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8') . __PS_BASE_URI__ . 'modules/' . $this->name . '/'));

    //            echo "Valor: ".$total." ID CART: ".$id_cart." Nome: ".$products[$key]['name']." Param: ".$params['objOrder']->id;


    return $this->display(__file__, 'payment.tpl');

}

public function getL($key, $cod_moip=false, $id_transacao=false, $email_consumidor=false) {

    if($cod_moip == false){
        $cod_moip  = $_POST['cod_moip'];
    }else{
        $cod_moip  = $cod_moip;
    }
    if($id_transacao == false){
        $id_transacao = $_POST['id_transacao'];
    }else{
        $id_transacao = $id_transacao;
    }
    if($$email_consumidor == false){
        $$email_consumidor = $_POST['email_consumidor'];
    }else{
        $email_consumidor = $email_consumidor;    
    }

    $translations = array(
        'valor_moip' => $this->l('Valor nao especificado corretamente.'),
        'status_pagamento_moip' => $this->l('Status do Pagamento nao definido corretamente, ou invalido.'),
        'payment' => $this->l('MoIP Pagamentos '),
        'id_transacao_moip' => $this->l('ID Proprio invalido ou nao relacionado a uma ordem de pagamento'),
        'email_consumidor_moip' => $this->l('E-Mail do cliente nao informado, POST invalido.'),
        'post_cod_moip' => $this->l('Codigo MoIP nao informado corretamente, ATENCAO ESSE POST PODE SER FRAUDOLENTO.'),
        'cart' => $this->l('Carrinho nao validado.'),
        'order' => $this->l('Transacao ja processada anteriormente com esse carrinho.'),
        'transaction' => $this->l('Pagamento processado pelo MoIP <br />Codigo MoIP: <b>' . $cod_moip . '</b><br />ID Proprio: <b>' . $id_transacao . '</b><br />E-mail utilizado na compra: <b>' . $email_consumidor . '</b>'),
        'verified' => $this->l('Transacao MoIP nao VERIFICADA.'),
        'mail' => $this->l('Processo de envio, email de notificacao.'),
    );
    return $translations[$key];
}


function validateOrder($id_cart, $id_order_state, $amountPaid, $paymentMethod = 'Unknown', $message = NULL, $extraVars = array(), $currency_special = false, $dont_touch_amount = false) {
    if (!$this->active)
        return;

    $currency = $this->getCurrency();
    $cart = new Cart(intval($id_cart));
    $cart->id_currency = $currency->id;
    $cart->save();
    parent::validateOrder($id_cart, $id_order_state, $amountPaid, $paymentMethod, $message, $extraVars, $currency_special, true);
}

public function addOrder($id_transaction) {
    return Db::getInstance()->Execute('
        INSERT INTO `' . _DB_PREFIX_ . 'moip_order` (`id_order`, `id_transaction`)
        VALUES(' . intval($this->currentOrder) . ', \'' . pSQL($id_transaction) . '\')');
}

public function getOrder($id_transaction) {
    $rq = Db::getInstance()->getRow('
        SELECT `id_order` FROM `' . _DB_PREFIX_ . 'moip_order`
        WHERE id_transaction = \'' . pSQL($id_transaction) . '\'');
    return $rq;
}

function getStatus($param) {
    global $cookie;

    $sql_status = Db::getInstance()->Execute
        ('
        SELECT `name`
        FROM `' . _DB_PREFIX_ . 'order_state_lang`
        WHERE `id_order_state` = ' . $param . '
        AND `id_lang` = ' . $cookie->id_lang . '

        ');

    return mysql_result($sql_status, 0);
}

public function enviar($mailVars, $template, $assunto, $DisplayName, $idCustomer, $idLang, $CustMail, $TplDir) {

    Mail::Send
        (intval($idLang), $template, $assunto, $mailVars, $CustMail, null, null, null, null, null, $TplDir);
}

public function getUrlByMyOrder($myOrder) {

    $module = Module::getInstanceByName($myOrder->module);
    $pagina_qstring = __PS_BASE_URI__ . "order-confirmation.php?id_cart="
        . $myOrder->id_cart . "&id_module=" . $module->id . "&id_order="
        . $myOrder->id . "&key=" . $myOrder->secure_key;

    if ($_SERVER['HTTPS'] != "on")
        $protocolo = "http";

    else
        $protocolo = "https";

    $retorno = $protocolo . "://" . $_SERVER['SERVER_NAME'] . $pagina_qstring;
    return $retorno;
}

public function newHistory($id_transacao_moip, $status, $errors=false){

    $KEY_NASP = Configuration::get('KEY_NASP');
    if (!empty($errors) AND isset($id_transacao_moip)){

        $id_order_db = $this->getOrder($id_transacao_moip);

        $id_transacao = $id_order_db['id_order'];
        $id_transacao_proprio = $id_order_db['id_transaction'];

        log_var("ID transacao: ".$id_transacao_moip."\nID Compra(order) PrestaShop: ".$id_transacao, "Recuperando order do BD(MoIP), Data: ".date("d-m-Y G:i:s"), true, $KEY_NASP);

        $extraVars = array();
        $history = new OrderHistory();
        $history->id_order = intval($id_transacao);
        $history->changeIdOrderState(intval($status), intval($id_transacao) );
        $history->addWithemail(true, $extraVars);

    }
}

public function newMoIPStatus($status_moip, $tipo=false){

    if($tipo != false){
        if ($status_moip == 1 || $status_moip == "Autorizado")
            $status = "Autorizado";
        elseif ($status_moip == 2 || $status_moip == "Iniciado")
            $status = "Iniciado";
        elseif ($status_moip == 3 || $status_moip == "BoletoImpresso")
            $status = "BoletoImpresso";
        elseif ($status_moip == 4 || $status_moip == "Concluido")
            $status = "Concluido";
        elseif ($status_moip == 5 || $status_moip == "Cancelado")
            $status = "Cancelado";
        elseif ($status_moip == 6 || $status_moip == "EmAnalise")
            $status = "EmAnalise";
        elseif ($status_moip == 7 || $status_moip == "Estornado")
            $status = "Estornado";             
    }else{
        if ($status_moip == 1 || $status_moip == "Autorizado")
            $status = Configuration::get('MoIP_STATUS_0');
        elseif ($status_moip == 2 || $status_moip == "Iniciado")
            $status = Configuration::get('MoIP_STATUS_1');
        elseif ($status_moip == 3 || $status_moip == "BoletoImpresso")
            $status = Configuration::get('MoIP_STATUS_2');
        elseif ($status_moip == 4 || $status_moip == "Concluido")
            $status = Configuration::get('MoIP_STATUS_3');
        elseif ($status_moip == 5 || $status_moip == "Cancelado")
            $status = Configuration::get('MoIP_STATUS_4');
        elseif ($status_moip == 6 || $status_moip == "EmAnalise")
            $status = Configuration::get('MoIP_STATUS_5');
        elseif ($status_moip == 7 || $status_moip == "Estornado")
            $status = Configuration::get('MoIP_STATUS_6');
    }

    return $status;
}

public function htmlRedirect($link, $time=0, $link_popUp=false, $link_api=false){

    $html .= '
        <html>
        <head>
        <script language="JavaScript">
<!--
function PagamentoCartaoCredito(redirectTo, timeoutPeriod) {';

if($link_popUp != false && $link_popUp != "boleto"){
    $html .= '
        window.open( \''.$link_popUp.'\' )
        ';
}

$html .='
    setTimeout("location.href = redirectTo;",timeoutPeriod);
}
//   -->
           </script>';



          $html .='
<style type="text/css">
<!--
body {
background: #ffffff url(loading.gif);
background-attachment: scroll;
background-repeat: no-repeat;
background-position: top center;

}

//-->
</style>

</head>
<body onload="JavaScript:PagamentoCartaoCredito(redirectTo=\''.$link.'\', timeoutPeriod=\''.$time.'\')">
</body>
</html> ';


        return $html;

    }


}

?>
