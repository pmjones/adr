<?= $this->ul()->items($this->blog->getMessages()) ?>

<?= $this->form(array(
    'method' => $this->method,
    'action' => $this->action,
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
                    'value' => $this->submit,
                ));
            ?></td>
        </tr>
    </table>
<?= $this->tag('/form') ?>
