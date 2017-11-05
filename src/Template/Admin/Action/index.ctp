<?php
$this->loadHelper('Backend.Chosen');
$this->loadHelper('Backend.DataTable');
?>
<div class="index">

    <div class="box">
        <div class="box-body">
            <?php
            $this->DataTable->create($this->get('dataTable'), $this->get('result'));
            echo $this->DataTable->renderAll();
            ?>
        </div>
    </div>

    <?php if (\Cake\Core\Configure::read('debug')): ?>
    <small><?php echo h(__FILE__); ?></small>
    <?php endif; ?>
</div>
