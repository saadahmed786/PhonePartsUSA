$.fn.followTo = function (pos) {
    var $this = this,
    $window = $(window);
    $window.scroll(function (e) {
        if ($window.scrollTop() > 	pos) {
            $this.css({
                position: 'absolute',
                top: pos,
                right:0
            });
        } else if($window.scrollTop() > ($this.height()/2)-50) {
            $this.css({
                position: 'fixed',
                top: 66,
                right:0,
            });
        }
        else if($window.scrollTop() < $this.height())
        {
        	$this.css({
                position: 'inherit'
            });
        }
    });
};
setTimeout(function(){
	if($('#parent_container_div').height() > 600)
	{
		$('#child_container_div').followTo($('#parent_container_div').height()-295);
	}
},500)