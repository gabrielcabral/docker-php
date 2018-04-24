<?php
$config = array(
    'content'     => '<div align="center"><img style="width: 150px; height: 150px;" src="http://dev.fnde.gov.br/static/images/logo/brasao_print.png"><h1>Produto de testes</h1></div>
<p>Nulla a tellus in erat ullamcorper tristique. Suspendisse at mauris. Sed ut eros. Proin fermentum. Nulla facilisi. Donec id dui quis libero porttitor mattis. Vivamus dapibus. Duis adipiscing interdum ante. Mauris nisi dui, ornare sed, elementum sed, aliquet eget, nisl. Quisque viverra lectus eget nibh. Duis justo erat, mollis nec, consectetur vel, interdum fermentum, neque. Aliquam eget eros sit amet libero ullamcorper luctus.</p>
<p>Nulla a tellus in erat ullamcorper tristique. 
Suspendisse at mauris. Sed ut eros. Proin fermentum. Nulla facilisi. 
Donec id dui quis libero porttitor mattis. Vivamus dapibus. Duis 
adipiscing interdum ante. Mauris nisi dui, ornare sed, elementum sed, 
aliquet eget, nisl. Quisque viverra lectus eget nibh. Duis justo erat, 
mollis nec, consectetur vel, interdum fermentum, neque. Aliquam eget 
eros sit amet libero ullamcorper luctus.</p>
<p>Nulla a tellus in erat ullamcorper tristique. 
Suspendisse at mauris. Sed ut eros. Proin fermentum. Nulla facilisi. 
Donec id dui quis libero porttitor mattis. Vivamus dapibus. Duis 
adipiscing interdum ante. Mauris nisi dui, ornare sed, elementum sed, 
aliquet eget, nisl. Quisque viverra lectus eget nibh. Duis justo erat, 
mollis nec, consectetur vel, interdum fermentum, neque. Aliquam eget 
eros sit amet libero ullamcorper luctus.</p>
<p>Nulla a tellus in erat ullamcorper tristique. 
Suspendisse at mauris. Sed ut eros. Proin fermentum. Nulla facilisi. 
Donec id dui quis libero porttitor mattis. Vivamus dapibus. Duis 
adipiscing interdum ante. Mauris nisi dui, ornare sed, elementum sed, 
aliquet eget, nisl. Quisque viverra lectus eget nibh. Duis justo erat, 
mollis nec, consectetur vel, interdum fermentum, neque. Aliquam eget 
eros sit amet libero ullamcorper luctus.</p>
<p align="right">Bras&iacute;lia, 17 de mar&ccedil;o de 2011.</p>',
    'orientation' => 'P',
    'paper' => 'A4',
    'filename' => 'exemple.pdf'
);
?>
<!DOCTYPE html>
<html id="home" lang="pt-Br">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <title>HTML2PDF :: Ambiente de teste</title>
    <script type="text/javascript" src="media/jquery.js"></script>
    <script type="text/javascript" src="media/jquery.cleditor.js"></script>
    <link rel="stylesheet" type="text/css" href="media/jquery.cleditor.css">

    <style type="text/css">
        fieldset {
            display: table;
        }
        legend {
            font-weight: bold;
            border: 1px dotted #000;
            background-color: #000;
            color: #fff;
        }
        label {
            font-weight: bold;
            float: left;
            margin-right: 8px;
        }
        label 
            span {
                display: block;
            }
        select.FocusOnFirstLetter option:first-letter {
            font-weight: bold;
        }
    </style>
</head>
<body>
<h2>HTML2PDF :: Ambiente de teste</h2>
<form action="parse.php" method="post">
  <fieldset>
    <legend>Configs</legend>
    <label>
      <span>Paper:</span>
      <select name="paper">
        <option>4A0</option>
        <option>2A0</option>
        <option>A0</option>
        <option>A1</option>
        <option>A2</option>
        <option>A3</option>
        <option selected="selected">A4</option>
        <option>A5</option>
        <option>A6</option>
        <option>A7</option>
        <option>A8</option>
        <option>A9</option>
        <option>A10</option>
        <option>B0</option>
        <option>B1</option>
        <option>B2</option>
        <option>B3</option>
        <option>B4</option>
        <option>B5</option>
        <option>B6</option>
        <option>B7</option>
        <option>B8</option>
        <option>B9</option>
        <option>B10</option>
        <option>C0</option>
        <option>C1</option>
        <option>C2</option>
        <option>C3</option>
        <option>C4</option>
        <option>C5</option>
        <option>C6</option>
        <option>C7</option>
        <option>C8</option>
        <option>C9</option>
        <option>C10</option>
        <option>RA0</option>
        <option>RA1</option>
        <option>RA2</option>
        <option>RA3</option>
        <option>RA4</option>
        <option>SRA0</option>
        <option>SRA1</option>
        <option>SRA2</option>
        <option>SRA3</option>
        <option>SRA4</option>
        <option>LETTER</option>
        <option>LEGAL</option>
        <option>EXECUTIVE</option>
        <option>FOLIO</option>
      </select>
    </label>
    <label>
      <span>Orientation:</span>
      <select name="orientation" class="FocusOnFirstLetter">
        <option value="P" selected="selected">
        <span>P</span>- Portrait</option>
        <option value="L">
        <span>L</span>- Landscape</option>
      </select>
    </label>
    <label>
      <span>Output mode:</span>
      <select name="output" class="FocusOnFirstLetter">
        <option value="I" selected="selected">
        <span>I</span>- Send PDF to the standard output</option>
        <option value="D">
        <span>D</span>- Download PDF as file</option>
      </select>
    </label>
    <label>
      <span>Filename:</span>
      <input type="text" name="filename" value="exemple.pdf"  size="65" />
    </label>
  </fieldset>
  <fieldset>
    <legend>Html</legend>
    <label>
      <span>Content:</span>
      <textarea name="content" class="htmlEditor" rows="20" cols="120"><?php echo $config['content'] ?></textarea>
    </label>
  </fieldset>
  <input type="submit" value="Enviar"/>
</form>
<script type="text/javascript">
    $(document).ready(function() {
        $('textarea.htmlEditor').cleditor({width:"100%", height:"350px"})[0].focus();
    });
</script>
</body>
</html>