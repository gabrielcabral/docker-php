<?php

class Fnde_Sice_View_Helper_DataTables {

	const INPUT_TYPE_CHECKBOX = 'checkbox';
	const INPUT_TYPE_RADIO = 'radio';

	const CLASS_ICONS = 'icons';
	const CLASS_INPUT = 'itemSelect';

	/**
	 * Cabeçalho da tabela
	 * @var array
	 */
	private $_header = null;

	/**
	 * Dados
	 * @var array
	 */
	private $_data = null;

	/**
	 * Chave usada nos links do metodo rowAction
	 * @var array
	 */
	private $_id = null;

	/**
	 * Rows action
	 * @var array
	 */
	private $_rowAction = null;

	/**
	 * Atributos da tabela
	 * @var array
	 */
	private $_tableAttribs = array();

	/**
	 * Titulo da tabela
	 * @var string
	 */
	private $_title = null;

	/**
	 * Colunas que serão escondidos
	 * @var array
	 */
	private $_columnsHidden = null;

	/**
	 * Flag row input
	 * @var boolean
	 */
	private $_flagRowInput = false;

	/**
	 * input que será utilizado ex. checkbox ou radio
	 * @var string
	 */
	private $_rowInput = null;

	/**
	 * Coluna ação
	 * @var string
	 */
	private $_actionColumn = 'Ações';

	/**
	 * Ações adicionais
	 * @var array
	 */
	private $_mainAction = array();

	/**
	 * Possui chechbox de seleção todos
	 * @var boolean
	 */
	private $_checkAll = true;
	/**
	 * @param array $_header
	 * @return Fnde_View_Helper_DataTable - fluent interface
	 */
	public function setHeader( array $_header ) {
		$this->_header = $_header;
		return $this;
	}

	/**
	 * @param array $_data
	 * @return Fnde_View_Helper_DataTable - fluent interface
	 */
	public function setData( array $_data ) {
		$this->_data = $_data;
		return $this;
	}

	/**
	 * @param string $_id
	 * @return Fnde_View_Helper_DataTable - fluent interface
	 */
	public function setId( $_id ) {
		$this->_id = $_id;
		return $this;
	}

	/**
	 * Adiciona rowAction por linha
	 * @param array $_rowAction
	 * @return Fnde_View_Helper_DataTable - fluent interface
	 */
	public function addRowAction( array $_rowAction ) {
		$this->_rowAction[] = $_rowAction;
		return $this;
	}

	/**
	 * @param array $_rowAction
	 * @return Fnde_View_Helper_DataTable - fluent interface
	 */
	public function setRowAction( array $_rowAction ) {
		$this->_rowAction = $_rowAction;
		return $this;
	}

	/**
	 * @param array $_tableAttribs
	 * @return Fnde_View_Helper_DataTable - fluent interface
	 */
	public function setTableAttribs( array $_tableAttribs ) {
		$this->_tableAttribs = $_tableAttribs;
		return $this;
	}

	/**
	 * @param string $_title
	 * @return Fnde_View_Helper_DataTables
	 */
	public function setTitle( $_title ) {
		$this->_title = $_title;
		return $this;
	}

	/**
	 * @param array $_columnsHidden
	 * @return Fnde_View_Helper_DataTables
	 */
	public function setColumnsHidden( array $_columnsHidden ) {
		$this->_columnsHidden = $_columnsHidden;
		return $this;
	}

	/**
	 * @param string $type
	 * @return Fnde_View_Helper_DataTables
	 */
	public function setRowInput( $type ) {
		$this->_flagRowInput = true;
		$this->_rowInput = $type;
		return $this;
	}

	/**
	 *
	 * @param type $_actionColumn
	 * @return Fnde_View_Helper_DataTables
	 */
	public function setActionColumn( $_actionColumn ) {
		$this->_actionColumn = $_actionColumn;
		return $this;
	}

	/**
	 * Adiciona as principais ações
	 * @param string $label
	 * @param string $url
	 * @return Fnde_View_Helper_DataTables
	 */
	public function addMainAction( $label, $url ) {
		$this->_mainAction[$label] = $url;
		return $this;
	}

	/**
	 * Ex.: array('visualizar' => 'url', 'relatorio' => 'url')
	 * @param array $mains
	 * @return Fnde_View_Helper_DataTables
	 */
	public function setMainAction( array $mains ) {
		foreach ( $mains as $label => $url )
			$this->_mainAction[$label] = $url;
		return $this;
	}

	/**
	 * @return boolean
	 */
	private function hasInput() {
		return $this->_flagRowInput ? true : false;
	}

	/**
	 * Retorna o Html
	 * @return string
	 */
	public function dataTables() {
		return new self();
	}

	/**
	 * Constroi o cabeçalho da tabela
	 * @return string
	 */
	private function buildHeader() {
		$header = '<tr>';

		if ( $this->hasInput() ) {
			$header .= '<th>';

			if ( $this->_checkAll ) {
				$header .= '<input type="checkbox" class="checkall" />';
			}

			$header .= '</th>';
		}

		$header .= '<th>' . implode('</th><th>', $this->_header) . '</th>';

		if ( !is_null($this->_rowAction) ) {
			$header .= '<th>' . $this->_actionColumn . '</th>';
		}

		$header .= '</tr>';

		return $header;
	}

	/**
	 * Constroi atributos
	 * @param array $arrAttribs
	 * @return string
	 */
	private function buildAttribs( array $arrAttribs ) {
		$attribs = '';

		if ( count($arrAttribs) > 0 ) {
			foreach ( $arrAttribs as $key => $value ) {
				$attribs .= " {$key}=\"{$value}\"";
			}
		}

		return $attribs;
	}

	/**
	 * Constroi o body
	 * @return string
	 */
	private function buildBody() {
		$body = '';

		$total = count($this->_data);
		for ( $i = 0; $i < $total; $i++ ) {

			$body .= '<tr>';

			if ( !is_null($this->_id) ) {
				if ( !array_key_exists($this->_id, $this->_data[$i]) ) {
					trigger_error("O id definido para a construção do grid {$this->_id} não existe!");
				}
			}

			if ( $this->hasInput() ) {
				$body .= '<td class="' . self::CLASS_INPUT . '">'
						. $this->buildInput($this->_id, $this->_data[$i][$this->_id]) . '</td>';
			}

			$links = '';
			if ( !is_null($this->_rowAction) ) {

				$links = ( isset($this->_rowAction[$i]) ? $this->buildRowAction($this->_rowAction[$i], $this->_data[$i])
						: $this->buildRowAction($this->_rowAction, $this->_data[$i]) );
			}

			$row = $this->columnsHidden($this->_data[$i]);

			$body .= '<td>' . implode('</td><td>', $row) . '</td>';

			if ( !empty($links) ) {
				$body .= '<td class="' . self::CLASS_ICONS . '">' . $links . '</td>';
			}

			$body .= '</tr>' . PHP_EOL;
		}

		return $body;
	}

	/**
	 * Constroi o rowAction
	 * @param type $rowAction
	 * @param type $data
	 * @return type
	 */
	private function buildRowAction( $rowAction, $data ) {
		$links = '';
		foreach ( $rowAction as $action ) {
			if ( isset($action['params']) ) {
				foreach ( $action['params'] as $column ) {
					if ( !array_key_exists($column, $data) ) {
						trigger_error("O indice definido para a construção do link do grid {$column} não existe!");
					}
					$params[] = $data[$column];
				}
				$links .= $this->buildLink($action, $params);
				unset($params);
			} else {
				$links .= $this->buildLink($action);
			}
		}

		return $links;
	}

	/**
	 * Constroi o input
	 * @param string $name
	 * @param string $value
	 * @return string
	 */
	private function buildInput( $name, $value ) {
		return "<input type=\"{$this->_rowInput}\" name=\"{$name}\" value=\"$value\" class=\"check_{$name}\" />";
	}

	/**
	 * Constroi um link que será utilizado na tabela
	 * @param string $action
	 * @param array $id
	 * @return string
	 */
	private function buildLink( $action, $id = array() ) {
		$link = '';
		$label = '';
		$attribs = '';

		if ( isset($action['label']) && !empty($action['label']) ) {
			$label = "<span>{$action['label']}</span>";
		}
		if ( isset($action['attribs']) && count($action['attribs']) > 0 ) {
			$attribs = $this->buildAttribs($action['attribs']);
		}

		if ( !empty($id) ) {
			$link .= '<a href=' . call_user_func_array('sprintf', array_merge(array($action['url']), $id))
					. " {$attribs}>" . $label . "</a>";
		} else {
			$link .= '<a href=' . $action['url'] . " {$attribs}>" . $label . "</a>";
		}

		return $link;
	}

	/**
	 * Constroi as ações do mainAction
	 * @return string
	 */
	private function buildMainAction() {
		$mainAction = '';

		if ( count($this->_mainAction) > 0 ) {
			$mainAction .= '<div class="listagemAcoes">' . PHP_EOL . '<label>' . PHP_EOL . '<span>+</span> ações: '
					. PHP_EOL . '<select name="table_main_action" id="table_main_action">' . PHP_EOL
					. '<option value="">Selecione...</option>' . PHP_EOL;
			foreach ( $this->_mainAction as $key => $value ) {
				$mainAction .= "<option value='{$value}'>{$key}</option>";
			}
			$mainAction .= '</select>' . PHP_EOL . '</label>' . PHP_EOL
					. '<input type="button" name="table_button" id="table_button" value="Ok" disabled="">' . PHP_EOL
					. '</div>';
		}

		return $mainAction;
	}

	/**
	 * Remove as colunas que não serão apresentadas
	 * @param array $row
	 * @return array
	 */
	private function columnsHidden( $row ) {
		if ( count($this->_columnsHidden) > 0 ) {
			foreach ( $this->_columnsHidden as $value ) {
				unset($row[$value]);
			}
		}

		return $row;
	}

	protected $_headerActive = true;
	public function setHeaderActive( $bool ) {
		$this->_headerActive = $bool;
		return $this;
	}

	protected $_footerActive = true;
	public function setFooterActive( $bool ) {
		$this->_footerActive = $bool;
		return $this;
	}

	/**
	 * settings to js options dataTables
	 * @var type
	 */
	protected $_tableSettings;
	public function setSettings( $string ) {
		$this->_tableSettings = $string;
		return $this;
	}

	/**
	 * Default não adicionar automaticamente o javascript para aplicar o Datatables.
	 **/
	protected $_autoCallJs = false;

	public function setAutoCallJs( $flag ) {
		$this->_autoCallJs = $flag;
	}

	public function getAutoCallJs() {
		return $this->_autoCallJs;
	}

	/**
	 * Renderiza a tabela
	 * @return string
	 */
	public function __toString() {
		$html = '';

		$attribs = $this->buildAttribs($this->_tableAttribs);
		$header = $this->buildHeader();
		$body = $this->buildBody();
		$mainAction = $this->buildMainAction();

		$html .= "
		<div class=\"listagem dataTable\">
		<table {$attribs}>
		<caption>{$this->_title}</caption>
		<thead>{$header}</thead>
		<tbody>
		{$body}
		</tbody>
		</table>
		{$mainAction}
		</div>";
		$headerActive = $this->_headerActive;
		$footerActive = $this->_footerActive;
		$jsSettings = empty($this->_tableSettings) ? 'null' : "'{$this->_tableSettings}'";
		if ( $this->_autoCallJs ) {
			$html .= "<script type='text/javascript'>Helper.DataTable('#{$this->_tableAttribs['id']}','{$headerActive}','{$footerActive}',{$jsSettings});</script>";
		}

		if ( $this->_checkAll ) {
			$html .= "<script type='text/javascript'>"
					. '
			function selecao(){
				pai = $(".checkall").parent();
				avo = pai.parent();
				pai.remove();
				avo.prepend("<th>"+pai.html()+"</th>");			
				$(".checkall").click(function(){
						val = $(this).attr("checked");
						$(".check_' . $this->_id . '").attr("checked",(val == "checked" || val == true ? "checked" : false) );
				});
			};
			
			$(document).ready(function(){
				setTimeout(\'selecao()\',500);
			});
			
			</script>
			';
		}

		return $html;
	}

	public function setCheckAll( $checkAll ) {
		$this->_checkAll = $checkAll;
	}
	public function getCheckAll() {
		return $this->_checkAll;
	}

}
