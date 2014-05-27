<?= $this->ul()->items($this->_blog->getMessages()) ?>

<?= $this->form(array(
    'method' => $this->_method,
    'action' => $this->_action,
)); ?>
    <table>
        <tr>
            <td>Title</td>
            <td><?=
                $this->input(array(
                    'type' => 'text',
                    'name' => 'blog[title]',
                    'value' => $this->_blog->title,
                ));
            ?></td>
        </tr>
        <tr>
            <td>Body</td>
            <td><?=
                $this->input(array(
                    'type' => 'textarea',
                    'name' => 'blog[body]',
                    'value' => $this->_blog->body,
                ));
            ?></td>
        </tr>
        <tr>
            <td colspan="2"><?=
                $this->input(array(
                    'type' => 'submit',
                    'value' => $this->_submit,
                ));
            ?></td>
        </tr>
    </table>
<?= $this->tag('/form') ?>
