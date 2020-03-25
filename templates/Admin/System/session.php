<?php $this->Breadcrumbs->add(__d('backend', 'Backend'), ['controller' => 'Backend', 'action' => 'index']); ?>
<?php $this->Breadcrumbs->add(__d('backend', 'Systeminfo'), ['action' => 'index']); ?>
<?php $this->Breadcrumbs->add(__d('backend', 'Session')); ?>
<div class="view">
    <h2><?= __d('backend', 'Session Info'); ?></h2>
    <?php echo $this->element('Backend.array_to_tablelist', ['data' => $this->get('session')]); ?>
</div>