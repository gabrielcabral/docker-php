<?php
/**
 * @category   Fnde
 * @package    View
 * @subpackage Helper_UserInfo
 */

class Fnde_View_Helper_Table extends Zend_View_Helper_Abstract
{
    /**
     * @param  string $caption
     * @param  array  $data
     * @param  array  $option
     * @return string
     */
    function table($caption, $data, $options = null)
    {
        $attribs = '';
        if (isset($options['header'])){
            $theader = $this->renderRow($options['header'],true);
        } else {
            $theader = array_slice($data,0,1);
            $theader = $this->renderRow(array_keys($theader[0]),true);
        }
        /**
         * @todo attribs para a table
         */
        if (isset($options['attribs'])){
            foreach($options['attribs'] as $key => $value){
                $attribs .= " {$key}=\"{$value}\"";
            }
        }

        $tbody = '';
        foreach($data as $row){
            $tbody .= $this->renderRow($row);
        }
        $tmpReturn = "
        <table{$attribs}>
            <caption>{$caption}</caption>
            <thead>{$theader}</thead>
            <tbody>{$tbody}</tbody>
        </table>
        ";
        
        return $tmpReturn;
    }
    
    private function renderRow($dataRow, $isHeader = false){
        if ($isHeader){
            $tmpRow = '<tr><th>' . implode('</th><th>',$dataRow) . '</th></tr>';
        } else {
            $tmpRow = '<tr><td>' . implode('</td><td>',$dataRow) . '</td></tr>';
        }

        return $tmpRow;
    }
}
