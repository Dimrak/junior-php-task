<h1>Cats show index page</h1>


<form action="/" method="get">
    <div>
        <label for="N-number" style="display: block;">Enter a number between 1 and 1000000</label>
        <input id="N-number" type="text" name="N">
    </div>
    <button id="show-cats" type="submit">Create new cat list</button>
</form>
<p>
    <?php if(!empty($this->cats)): ?>
        <?= $this->cats ?>
    <?php endif ?>
</p>
<!-- Validation messages -->
<?php if(isset($_SESSION['validation'])): ?>
    <mark><?= $_SESSION['validation']; ?></mark>
<?php  endif ?>



<!-- CountAll -->
<h2>List of pages visited </h2>
<?php  if(!empty($this->visits)): ?>
    <?= $this->visits ?>
<?php endif ?>

<!-- Individual visited pages -->
<table>
    <thead>
        <tr>
            <th>
                List of pages visited with counter
            </th>
        </tr>
    </thead>
    <tbody>
    <?php if(!empty($this->count_n_final)): ?>
        <?php foreach ($this->count_n_final as $key => $value):?>
            <tr>
                <td>
                    <mark>N page visited = <?= $key ?></mark>
                    <mark>Number of times visited = <?= $value ?></mark>
                </td>
            </tr>
        <?php endforeach ?>
    <?php endif ?>
    </tbody>
</table>


<a href="<?= url('cat/file_display'); ?>">See json file</a>



