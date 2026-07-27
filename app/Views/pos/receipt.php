<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - <?php echo $sale['invoice_number']; ?></title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
            background: white;
        }
        .receipt {
            border: 1px solid #000;
            padding: 20px;
            text-align: center;
        }
        .store-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .divider {
            border-top: 1px dashed #000;
            margin: 10px 0;
        }
        .receipt-header {
            font-size: 12px;
            margin-bottom: 15px;
        }
        .items-table {
            width: 100%;
            font-size: 11px;
            margin: 10px 0;
        }
        .item-row {
            display: flex;
            justify-content: space-between;
            padding: 4px 0;
        }
        .item-name {
            text-align: left;
        }
        .item-qty {
            text-align: center;
        }
        .item-total {
            text-align: right;
        }
        .totals {
            margin-top: 10px;
            font-size: 12px;
        }
        .total-line {
            display: flex;
            justify-content: space-between;
            padding: 4px 0;
        }
        .grand-total {
            font-weight: bold;
            font-size: 14px;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px dashed #000;
        }
        .payment-info {
            font-size: 11px;
            margin-top: 10px;
        }
        .footer {
            font-size: 10px;
            color: #666;
            margin-top: 20px;
        }
        @media print {
            body {
                margin: 0;
                padding: 0;
            }
        }
    </style>
</head>
<body onload="window.print();">
    <div class="receipt">
        <div class="store-name">📦 Stock & Sales</div>
        <div class="receipt-header">
            <div>Invoice: <?php echo SecurityHelper::escapeHtml($sale['invoice_number']); ?></div>
            <div>Date: <?php echo FormatterHelper::formatDate($sale['created_at']); ?></div>
            <div>Cashier: <?php echo SecurityHelper::escapeHtml($sale['full_name']); ?></div>
        </div>
        
        <div class="divider"></div>
        
        <div class="items-table">
            <?php foreach ($items as $item): ?>
            <div class="item-row">
                <span class="item-name"><?php echo SecurityHelper::escapeHtml(FormatterHelper::truncateText($item['name'], 15)); ?></span>
                <span class="item-qty" style="text-align: center;"><?php echo $item['quantity']; ?></span>
                <span class="item-total" style="text-align: right;"><?php echo FormatterHelper::formatCurrency($item['subtotal']); ?></span>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="divider"></div>
        
        <div class="totals">
            <div class="total-line">
                <span>Subtotal:</span>
                <span><?php echo FormatterHelper::formatCurrency($sale['subtotal']); ?></span>
            </div>
            <?php if ($sale['discount'] > 0): ?>
            <div class="total-line">
                <span>Discount:</span>
                <span>-<?php echo FormatterHelper::formatCurrency($sale['discount']); ?></span>
            </div>
            <?php endif; ?>
            <?php if ($sale['tax'] > 0): ?>
            <div class="total-line">
                <span>Tax:</span>
                <span><?php echo FormatterHelper::formatCurrency($sale['tax']); ?></span>
            </div>
            <?php endif; ?>
            
            <div class="grand-total">
                <div class="total-line">
                    <span>TOTAL:</span>
                    <span><?php echo FormatterHelper::formatCurrency($sale['total']); ?></span>
                </div>
            </div>
        </div>
        
        <div class="payment-info">
            <div>Payment: <?php echo ucfirst($sale['payment_method']); ?></div>
            <div>Received: <?php echo FormatterHelper::formatCurrency($sale['amount_received']); ?></div>
            <div>Change: <?php echo FormatterHelper::formatCurrency($sale['change']); ?></div>
        </div>
        
        <div class="divider"></div>
        
        <div class="footer">
            Thank you for your purchase!
            <br>
            <?php echo date('Y-m-d H:i:s'); ?>
        </div>
    </div>
</body>
</html>
