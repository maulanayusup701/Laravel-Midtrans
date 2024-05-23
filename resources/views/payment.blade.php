<!DOCTYPE html>
<html>

<head>
    <title>Payment</title>
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}">
    </script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>

<body>
    <button id="pay-button">Pay!</button>

    <script type="text/javascript">
        $('#pay-button').click(function(event) {
            event.preventDefault();
            $.post("{{ secure_url('payment/charge') }}", {
                    _token: '{{ csrf_token() }}'
                },
                function(data, status) {
                    snap.pay(data.snap_token);
                });

            if (data.transaction_status == 'capture' && data.fraud_status == 'accept') {
                window.location.href = "{{ secure_url('payment/success') }}";
            }

            if (data.transaction_status == 'settlement' && data.fraud_status == 'accept') {
                window.location.href = "{{ secure_url('payment/success') }}";
            }
        });
    </script>
</body>

</html>
