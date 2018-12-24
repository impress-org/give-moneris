document.addEventListener( 'DOMContentLoaded', () => {

    const donationStatus = document.getElementById( 'give-payment-status' );

    if ( null !== donationStatus ) {
        donationStatus.addEventListener( 'change', ( event ) => {

            document.getElementById( 'give-moneris-opt-refund' ).checked = false;

            // If donation status is complete, then show refund checkbox
            if ( 'refunded' === event.target.value ) {
                document.getElementById( 'give-moneris-opt-refund-wrap' ).style.display = 'block';
            } else {
                document.getElementById( 'give-moneris-opt-refund-wrap' ).style.display = 'none';
            }
        });
    }
});