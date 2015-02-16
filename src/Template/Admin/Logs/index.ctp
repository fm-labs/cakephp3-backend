<?php $this->Html->addCrumb(__('Backend'), ['controller' => 'Backend', 'action' => 'index']); ?>
<?php $this->Html->addCrumb(__d('backend', 'Logs')); ?>
<div class="index">
	<h2><?= __d('backend', 'LogViewer'); ?></h2>
	
	<table class="ui table striped">
        <thead>
            <tr>
                <th><?= __d('backend', 'Logfile');?></th>
                <th><?= __d('backend', 'Filesize');?></th>
                <th><?= __d('backend', 'Last modified');?></th>
                <th><?= __d('backend', 'Last access');?></th>
                <th class="actions"><?= __d('backend', 'Actions'); ?></th>
            </tr>
        </thead>
        <?php foreach ($this->get('files') as $file):?>
            <?php $id = basename($file['name']); ?>
            <tr>
                <td><?= $this->Html->link($file['name'], ['action' => 'view', $id]); ?></td>
                <td><?= $this->Number->toReadableSize($file['size']); ?></td>
                <td><?= $this->Time->timeAgoInWords($file['last_modified']); ?></td>
                <td><?= $this->Time->timeAgoInWords($file['last_access']); ?></td>
                <td class="actions">
                    <ul class="actions">
                        <li><?= $this->Html->link(__d('backend', 'View'), ['action' => 'view', $id]); ?></li>
                        <li><?= $this->Html->link(__d('backend', 'Clear'), ['action' => 'clear', $id]); ?></li>
                        <li><?= $this->Html->link(__d('backend', 'Delete'), ['action' => 'delete', $id]); ?></li>
                    </ul>
                </td>
            </tr>
        <?php endforeach; ?>
	</table>
	
	<br />
	<hr />
	<br />
	
	<h2>LogRotation</h2>
	<table class="ui table striped">
        <thead>
            <tr>
                <th><?= __d('backend', 'Alias');?></th>
                <th><?= __d('backend', 'Path');?></th>
                <th><?= __d('backend', 'Keep');?></th>
                <th><?= __d('backend', 'Schedule');?></th>
                <th><?= __d('backend', 'Compress');?></th>
                <th><?= __d('backend', 'Compress Delay');?></th>
                <th><?= __d('backend', 'Rotate Empty');?></th>
                <th><?= __d('backend', 'Email To');?></th>
                <th class="actions"><?= __d('backend', 'Actions'); ?></th>
            </tr>
        </thead>
		<?php foreach ($this->get('logRotation') as $alias => $config):?>
            <tr>
                <td><?= $alias; ?></td>
                <td><?= $config['path']; ?></td>
                <td><?= $config['keep']; ?></td>
                <td><?= $config['schedule']; ?></td>
                <td><?= $config['compress']; ?></td>
                <td><?= $config['compress_delay']; ?></td>
                <td><?= $config['rotate_empty']; ?></td>
                <td><?= $config['email_to']; ?></td>
                <td class="actions">
                    <ul>
                    <li><?= $this->Html->link('Rotate', ['action' => 'rotate', $alias])?></li>
                    <li><?= $this->Html->link('Force', ['action' => 'rotate', $alias, 'force'=>true]); ?></li>
                    </ul>
                </td>
            </tr>
		<?php endforeach; ?>
	</table>
	
</div>