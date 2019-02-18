<a onclick="applyVouchers();" class="button" style="float: right;"><span>Use Selected</span></a>
<div style="clear: both;"></div>
<div class="grid">
    <div class="row">
        <!-- voucher list -->
        <div class="voucher_list">
            <table class="table striped">
                <thead>
                    <tr>
                        <th style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></th>
                        <th>Code</th>
                        <th>Amount</th>
                        <th>Balnance</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($rows) { ?>

                    <?php foreach($rows as $row) { ?>
                    <tr class="data_row">
                        <?php if($row['code']) { ?>
                        <td align='center'><input type="checkbox" class="voucherCode" name="selected[]" value="<?= $row['code'] ?>" /></td>
                        <?php } ?>
                        <td><?= $row['code'] ?></td>
                        <td><?php echo $this->currency->format($row['amount']) ?></td>
                        <td align='right' class='td_total'><?php echo $this->currency->format($row['balance']) ?></td>
                        <td><?php echo ($row['status']) ? 'Enabled': 'Disabled'; ?></td>
                        <td align="center">
                            <a class="select" onclick="applySVoucher('<?= $row['code']; ?>');" href="#">Use</a>
                        </td>
                    </tr>
                    <?php } ?>
                    <?php } else { ?>
                    <tr class="data_row">
                        <td colspan="8" align="center">No Vouchers Available</td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <!-- END .Voucher_list -->

    </div>
</div>   
<!-- END .grid -->
