<html>
    <head>
        <link rel="stylesheet" href="/web/bootstrap/css/bootstrap.css">
        <script src="/web/bootstrap/js/bootstrap.bundle.js"></script>
    </head>
    <body>
        <div class="container">
            <div class="row">
            <div class="col-sm">
                <form method="post">
                    <div class="form-group">
                        <label for="current_year">Year</label>
                        <select name="current_year" id="current_year" class="form-control">
                            <?php foreach ($years as $year) {?>
                                <option
                                    value="<?=$year?>"
                                    <?php if ($currentYear === $year) {echo 'selected';}?>
                                >
                                    <?=$year?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="currency">Currency<?=(null !== $currencyAutodetected ? ' (Autodetected)' : '')?></label>
                        <select name="currency" id="currency" class="form-control">
                            <?php foreach ($currencies as $currencyName) {?>
                                <option
                                    value="<?=$currencyName?>"
                                    <?php if ($currency === $currencyName) {echo 'selected';}?>
                                >
                                    <?=$currencyName?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="price">Price</label>
                        <input type="text" name="price" id="price" class="form-control" value="<?=$price?>">
                    </div>
                    <input type="submit" class="btn btn-primary">
                </form>
            </div>
            </div>

            <div class="row">
            <div class="col-sm">
                <table class="table">
                    <tr>
                        <th>Currency</th>
                        <th>Price</th>
                        <th>Price above tax free</th>
                        <th>Tax</th>
                        <th>Price + tax</th>
                    </tr>
                    <?php
                    foreach ($result as $currency => $value) {
                        ?>
                        <tr>
                            <td><?=$currency?></td>
                            <td><?=$value['price']?></td>
                            <td><?=$value['price_above_tax_free_value']?></td>
                            <td><?=$value['tax']?></td>
                            <td><?=$value['price_with_tax']?></td>
                        </tr>
                        <?php
                    }
                    ?>
                </table>
            </div>
            </div>
        </div>
    </body>
</html>
