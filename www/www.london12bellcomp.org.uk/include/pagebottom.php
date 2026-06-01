</div>
</div>
</div>

<script type="text/javascript">
$(function() { 
	var path = location.pathname;
    $(".curved").each(function(){$(this).corner();});
	$("audio").bind("play pause ended", function(e){
			ga("send", "event", "touch " + e.type, $(this).attr('year'), $(this).attr('band'));
		});
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

</body>
</html>