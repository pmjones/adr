<?php if (isset($this->messages)) {
    echo $this->ul()->items($this->messages);
} ?>

<?= $this->form(array(
    'method' => $method,
    'action' => $action,
)); ?>
    <table>
        <tr>
            <td>Title</td>
            <td><?=
                $this->input(array(
                    'type' => 'text',
                    'name' => 'blog[title]',
                    'value' => $this->blog->title,
                ));
            ?></td>
        </tr>
        <tr>
            <td>Intro</td>
            <td><?=
                $this->input(array(
                    'type' => 'text',
                    'name' => 'blog[intro]',
                    'value' => $this->blog->intro,
                ));
            ?></td>
        </tr>
        <tr>
            <td>Body</td>
            <td><?=
                $this->input(array(
                    'type' => 'textarea',
                    'name' => 'blog[body]',
                    'value' => $this->blog->body,
                ));
            ?></td>
        </tr>
        <tr>
            <td colspan="2"><?=
                $this->input(array(
                    'type' => 'submit',
                    'value' => $submit,
                ));
            ?></td>
        </tr>
    </table>
<?= $this->tag('/form') ?>
