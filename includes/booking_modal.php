<div id="bookingModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2 class="modal-title">Book Your Experience</h2>
        <form id="bookingForm" class="booking-form">
            <input type="hidden" id="attraction_name" name="attraction_name">
            <input type="hidden" id="booking_type" name="booking_type">
            
            <div class="form-group">
                <label for="booking_date">Date</label>
                <input type="date" id="booking_date" name="booking_date" required min="<?php echo date('Y-m-d'); ?>">
            </div>
            
            <div class="form-group">
                <label for="number_of_people">Number of People</label>
                <select id="number_of_people" name="number_of_people" required>
                    <?php for($i = 1; $i <= 10; $i++): ?>
                        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                    <?php endfor; ?>
                    <option value="11">More than 10 (Please specify in notes)</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="special_requests">Special Requests</label>
                <textarea id="special_requests" name="special_requests" rows="3" placeholder="Any special requirements or notes for your booking..."></textarea>
            </div>
            
            <div class="booking-summary">
                <h3>Booking Summary</h3>
                <div class="summary-item">
                    <span>Attraction:</span>
                    <span id="summary_attraction"></span>
                </div>
                <div class="summary-item">
                    <span>Type:</span>
                    <span id="summary_type"></span>
                </div>
                <div class="summary-item">
                    <span>Date:</span>
                    <span id="summary_date"></span>
                </div>
                <div class="summary-item">
                    <span>People:</span>
                    <span id="summary_people"></span>
                </div>
                <div class="summary-item total">
                    <span>Estimated Total:</span>
                    <span id="summary_total"></span>
                </div>
            </div>
            
            <div class="form-actions">
                <button type="button" class="btn btn-outline modal-cancel">Cancel</button>
                <button type="submit" class="btn btn-primary">Confirm Booking</button>
            </div>
        </form>
    </div>
</div>

<style>
/* Modal Styles */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.6);
    animation: fadeIn 0.3s;
}

.modal-content {
    background-color: #fff;
    margin: 5% auto;
    padding: 30px;
    border-radius: 10px;
    width: 90%;
    max-width: 600px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    animation: slideIn 0.3s;
}

.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
    transition: 0.2s;
}

.close:hover {
    color: #333;
}

.modal-title {
    margin-top: 0;
    color: #333;
    font-family: 'Playfair Display', serif;
    border-bottom: 1px solid #eee;
    padding-bottom: 15px;
    margin-bottom: 20px;
}

.booking-summary {
    background-color: #f9f9f9;
    padding: 15px;
    border-radius: 5px;
    margin: 20px 0;
}

.summary-item {
    display: flex;
    justify-content: space-between;
    margin-bottom: 8px;
}

.summary-item.total {
    margin-top: 15px;
    padding-top: 10px;
    border-top: 1px dashed #ddd;
    font-weight: bold;
}

/* Animations */
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideIn {
    from { transform: translateY(-50px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}
</style> 