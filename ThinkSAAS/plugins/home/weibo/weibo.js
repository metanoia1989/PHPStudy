function sendweibo(){
	var title = $("#weibotitle").val();
	
	if(title==''){
		tsNotice('发布内容不能为空！');return false;
	}
	
	$("#weibosend").attr('disabled','disabled');
	
	$.post(siteUrl+'index.php?app=weibo&ac=ajax&ts=add',{'title':title},function(rs){
		if(rs==0){
			
			tsNotice('请登陆后再发布唠叨！');
			
		}else if(rs==1){
			
			tsNotice('发布内容不能为空！');
			
		}else if(rs==2){
			$("#weibotitle").val('');
			$("#weibosend").removeAttr('disabled');
			weibolist();
		
		}
	});
}

function weibolist(){
	$.get(siteUrl+'index.php?app=weibo&ac=ajax&ts=list',function(rs){
		$("#weibolist").html(rs);
	})
}