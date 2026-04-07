@extends('admin.layout.main')

@section('title', 'Order Details')
@section('page_title', 'Orders')

@section('content')
<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 32px; align-items: start;">
    <!-- Order Breakdown -->
    <div>
        <div class="section-card">
            <div class="section-header">
                <h3>Order #ORD-8942</h3>
                <span class="badge badge-success">Completed</span>
            </div>
            <div style="padding: 24px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 32px; border-bottom: 1px solid var(--border-color); padding-bottom: 24px;">
                    <div>
                        <p style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 4px;">User Information</p>
                        <p style="font-weight: 700; color: var(--primary);">Jane Cooper</p>
                        <p style="color: var(--text-muted);">jane@example.com</p>
                    </div>
                    <div style="text-align: right;">
                        <p style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 4px;">Order Date</p>
                        <p style="font-weight: 700; color: var(--primary);">Mar 14, 2026, 10:45 AM</p>
                    </div>
                </div>

                <div class="table-responsive">
                    <table style="border: none;">
                        <thead style="background: var(--bg-tertiary);">
                            <tr>
                                <th>Item Description</th>
                                <th style="text-align: center;">Qty</th>
                                <th style="text-align: right;">Price</th>
                                <th style="text-align: right;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="font-weight: 700;">24K Pure Gold Bullion (999)</td>
                                <td style="text-align: center;">100g</td>
                                <td style="text-align: right;">₹7,245.00/g</td>
                                <td style="text-align: right; font-weight: 800;">₹7,24,500.00</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" style="text-align: right; padding-top: 32px; color: var(--text-muted);">Subtotal:</td>
                                <td style="text-align: right; padding-top: 32px; font-weight: 700;">₹7,24,500.00</td>
                            </tr>
                            <tr>
                                <td colspan="3" style="text-align: right; border: none; color: var(--text-muted);">Tax / Charges:</td>
                                <td style="text-align: right; border: none; font-weight: 700;">₹0.00</td>
                            </tr>
                            <tr>
                                <td colspan="3" style="text-align: right; border: none; font-size: 1.2rem; font-weight: 800; color: var(--primary);">Grand Total:</td>
                                <td style="text-align: right; border: none; font-size: 1.2rem; font-weight: 800; color: var(--accent);">₹7,24,500.00</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Tracking / Actions -->
    <div>
        <div class="section-card">
            <div class="section-header">
                <h3>Track Order</h3>
            </div>
            <div style="padding: 24px;">
                <div style="position: relative; padding-left: 32px;">
                    <div style="position: absolute; left: 0; top: 0; bottom: 0; width: 2px; background: var(--accent);"></div>
                    
                    <div style="margin-bottom: 24px; position: relative;">
                        <div style="position: absolute; left: -36px; top: 0; width: 10px; height: 10px; background: var(--accent); border-radius: 50%;"></div>
                        <p style="font-weight: 700; color: var(--primary);">Payment Received</p>
                        <p style="font-size: 0.8rem; color: var(--text-muted);">Mar 14, 10:50 AM</p>
                    </div>
                    <div style="margin-bottom: 24px; position: relative;">
                        <div style="position: absolute; left: -36px; top: 0; width: 10px; height: 10px; background: var(--accent); border-radius: 50%;"></div>
                        <p style="font-weight: 700; color: var(--primary);">Order Processed</p>
                        <p style="font-size: 0.8rem; color: var(--text-muted);">Mar 14, 11:15 AM</p>
                    </div>
                    <div style="position: relative;">
                        <div style="position: absolute; left: -36px; top: 0; width: 10px; height: 10px; background: var(--accent); border-radius: 50%;"></div>
                        <p style="font-weight: 700; color: var(--primary);">Delivered</p>
                        <p style="font-size: 0.8rem; color: var(--text-muted);">Mar 14, 02:30 PM</p>
                    </div>
                </div>
                
                <button class="btn btn-primary" style="width: 100%; margin-top: 32px;">Print Invoice</button>
            </div>
        </div>
    </div>
</div>
@endsection
