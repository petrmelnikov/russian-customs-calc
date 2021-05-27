<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <link rel="stylesheet" href="/web/bootstrap/css/bootstrap.css">
        <script src="/web/bootstrap/js/bootstrap.bundle.js"></script>
        <script src="/web/js.js"></script>
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
                        <div class="form-group">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="calculate_shipping" name="calculate_shipping" <?=$calculateShipping ? 'checked' : ''?>>
                                <label class="form-check-label" for="calculate_shipping">
                                    Calculate shipping (<a href="https://polexp.com/calc.html" target="_blank">polexp.com/calc.html</a>)
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="shipping_type">Shipping Type</label>
                            <select name="shipping_type" id="shipping_type" class="form-control">
                                <?php foreach ($shippingTypes as $shippingTypeName) {?>
                                    <option
                                            value="<?=$shippingTypeName?>"
                                        <?php if ($shippingType === $shippingTypeName) {echo 'selected';}?>
                                    >
                                        <?=$shippingTypeName?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="weight">Weight (kg)</label>
                            <input type="text" name="weight" id="weight" class="form-control" value="<?=$weight?>">
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
                        <th>Shipping</th>
                        <th>Total</th>
                    </tr>
                    <?php
                    /**
                     * @var \App\DataTransferObjects\CalculationResult $calculationResult
                     */
                    foreach ($calculationResults as $currency => $calculationResult) {
                        ?>
                        <tr>
                            <td><?=$currency?></td>
                            <td><?=$calculationResult->getPrice()?></td>
                            <td><?=$calculationResult->getPriceAboveTaxFree()?></td>
                            <td><?=$calculationResult->getTax()?></td>
                            <td><?=$calculationResult->getPriceWithTax()?></td>
                            <td><?=$calculationResult->getShipping()?></td>
                            <td><?=$calculationResult->getTotal()?></td>
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
