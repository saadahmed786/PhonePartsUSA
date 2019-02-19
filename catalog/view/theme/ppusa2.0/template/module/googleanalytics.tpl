
<script type='text/javascript'>
$(document).on('click','#button-confirm', function () {
$.ajax({
type: 'GET',
url: 'index.php?route=module/googleanalytics/ajax_copy_order_data'
});
});
</script>
<script type='text/javascript'>
var _gaq = _gaq || [];
_gaq.push(['_setAccount', 'UA-24721193-1']);
_gaq.push(['_trackPageview']);
(function() {
var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
})();
</script>