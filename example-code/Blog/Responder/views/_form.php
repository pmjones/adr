<?= $this->ul()->items($blog->getMessages())->get(); ?>

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
                    'value' => $blog->title,
                ));
            ?></td>
        </tr>
        <tr>
            <td>Body</td>
            <td><?=
                $this->input(array(
                    'type' => 'textarea',
                    'name' => 'blog[body]',
                    'value' => $blog->body,
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
