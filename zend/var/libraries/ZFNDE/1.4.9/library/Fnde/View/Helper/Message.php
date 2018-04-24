<?php
/**
 * @category   Fnde
 * @package    View
 * @subpackage Helper_Message
 */
class Fnde_View_Helper_Message {

    protected static $titles = array(
        MSG_ERRO => 'Erro',
        MSG_ORIENTACAO => 'Orientação',
        MSG_ALERTA => 'Alerta',
        MSG_SUCESSO => 'Sucesso',
    );

    /**
     * @param  array  $messages
     * @param  string $title
     * @return string
     */
    function message(array $arrMessages) {
        $tmpReturn = '';
        if (count($arrMessages)) {
            foreach ($arrMessages as $namespace => $messages) {
                if (!empty($messages[0])) {
                    $tmpReturn .= '<div class="' . $namespace . '">';
                    if(is_array($messages[0])){
                        $tmpReturn .= '<h3>'.$messages[0]['title'].'</h3>';
                        $tmpReturn .= '<p>' . $messages[0]['msg'] . '</p>';
                    }else{
                        $tmpReturn .= '<h3>' . self::$titles[$namespace] . '</h3>';
                        $tmpReturn .= '<p>' . $messages[0]. '</p>';
                    }
                    $tmpReturn .= '</div>';
                }
            }
            if ($tmpReturn) {
                $tmpReturn = '<div id="mensagens">' . $tmpReturn . '</div>';
            }
        }
        return $tmpReturn;
    }
}
