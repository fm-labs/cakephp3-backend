<%
use Cake\Utility\Inflector;

$fields = collection($fields)
    ->filter(function($field) use ($schema) {
        return !in_array($schema->columnType($field), ['binary', 'text']);
    })
    ->take(7);
%>
<?php $this->Html->addCrumb(__('<%= $pluralHumanName %>')); ?>
<div class="actions">
    <div class="ui secondary menu">
        <div class="item"></div>
        <div class="right menu">
            <div class="item">
                <i class="add icon"></i>
                <?= $this->Html->link(__('New {0}', __('<%= $singularHumanName %>')), ['action' => 'add']) ?>
            </div>
            <div class="ui dropdown item">
                <i class="dropdown icon"></i>
                <i class="tasks icon"></i>Actions
                <div class="menu">
<%
                    $done = [];
                    foreach ($associations as $type => $data):
                        foreach ($data as $alias => $details):
                            if ($details['controller'] != $this->name && !in_array($details['controller'], $done)):
%>
                    <div class="item">
                        <i class="list icon"></i>
                        <?= $this->Html->link(__('List {0}', __('<%= $this->_pluralHumanName($alias) %>')), ['controller' => '<%= $details["controller"] %>', 'action' => 'index']) ?>
                    </div>
                    <div class="item">
                        <i class="add icon"></i>
                        <?= $this->Html->link(__('New {0}', __('<%= $this->_singularHumanName($alias) %>')), ['controller' => '<%= $details["controller"] %>', 'action' => 'add']) ?>
                    </div>
<%
                                $done[] = $details['controller'];
                            endif;
                        endforeach;
                    endforeach;
%>
<% if (empty($associations)) { %>
                    <div class="item">No Actions</div>
<% } %>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="ui divider"></div>

<div class="<%= $pluralVar %> index">
    <table class="ui table striped">
    <thead>
        <tr>
    <% foreach ($fields as $field): %>
        <th><?= $this->Paginator->sort('<%= $field %>') ?></th>
    <% endforeach; %>
        <th class="actions"><?= __('Actions') ?></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($<%= $pluralVar %> as $<%= $singularVar %>): ?>
        <tr>
<%        foreach ($fields as $field) {
            $isKey = false;
            if (!empty($associations['BelongsTo'])) {
                foreach ($associations['BelongsTo'] as $alias => $details) {
                    if ($field === $details['foreignKey']) {
                        $isKey = true;
%>
            <td>
                <?= $<%= $singularVar %>->has('<%= $details['property'] %>') ? $this->Html->link($<%= $singularVar %>-><%= $details['property'] %>-><%= $details['displayField'] %>, ['controller' => '<%= $details['controller'] %>', 'action' => 'view', $<%= $singularVar %>-><%= $details['property'] %>-><%= $details['primaryKey'][0] %>]) : '' ?>
            </td>
<%
                        break;
                    }
                }
            }
            if ($isKey !== true) {
                if (!in_array($schema->columnType($field), ['integer', 'biginteger', 'decimal', 'float'])) {
%>
            <td><?= h($<%= $singularVar %>-><%= $field %>) ?></td>
<%
                } else {
%>
            <td><?= $this->Number->format($<%= $singularVar %>-><%= $field %>) ?></td>
<%
                }
            }
        }

        $pk = '$' . $singularVar . '->' . $primaryKey[0];
%>
            <td class="actions">
                <div class="ui basic mini buttons">
                    <div class="ui button">
                        <?= $this->Html->link(__('View'), ['action' => 'view', <%= $pk %>]) ?>
                    </div>
                    <div class="ui floating dropdown icon button">
                        <i class="dropdown icon"></i>
                        <div class="menu">
                            <div class="item"><i class="edit icon"></i>
                                <?= $this->Html->link(__('Edit'), ['action' => 'edit', <%= $pk %>]) ?>
                            </div>
                            <div class="item"><i class="delete icon"></i>
                                <?= $this->Form->postLink(
                                    __('Delete'),
                                    ['action' => 'delete', <%= $pk %>],
                                    ['confirm' => __('Are you sure you want to delete # {0}?', <%= $pk %>)]
                                ) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </td>
        </tr>

    <?php endforeach; ?>
    </tbody>
    </table>
    <div class="paginator">
        <div class="ui pagination menu">
            <?= $this->Paginator->prev(__('previous')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('next')) ?>

            <div class="item">
                <?= $this->Paginator->counter() ?>
            </div>
        </div>
    </div>
</div>