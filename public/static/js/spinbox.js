function spinbox(obj){
	$(".spinbox-up").click(function(){
		var count=obj.val();
		count++;
		obj.val(count);
	})
	$(".spinbox-down").click(function(){
		var count=obj.val();
		if(count>1)count--;
		obj.val(count);
	})
}