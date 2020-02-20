<script type="text/javascript">
    jQuery(document).ready(function() {
        /* Fix DOM load setting timeout before actions*/
        setTimeout(function(){
            //add attribute name to coneckta payment gateway form with the aim of being send on post data
            jQuery('#conekta-card-number').attr('name','conekta-card-number');
            jQuery('#conekta-card-name').attr('name','conekta-card-name');
            jQuery('#card_expiration').attr('name','card-expiration-month');
            jQuery('#card_expiration_yr').attr('name','card-expiration-year');
            jQuery('#conekta-card-cvc').attr('name','conekta-card-cvc');
        }, 2000);
    });
</script>