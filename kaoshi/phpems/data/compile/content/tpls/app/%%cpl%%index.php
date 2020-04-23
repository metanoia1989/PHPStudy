<?php $this->_compileInclude('header'); ?>
<body>
<div class="container-fluid">
	<div class="row-fluid">
		<div class="pages">
            <?php $this->_compileInclude('nav'); ?>
			<div class="content">
				<div class="col-xs-9">
                    <?php $cid = 0;
 foreach($this->tpl_var['contents'] as $key => $contents){ 
 $cid++; ?>
					<div class="content-box padding">
						<h2 class="title">
                            <?php echo $this->tpl_var['catids'][$key]['catname']; ?>
							<a href="index.php?content-app-category&catid=<?php echo $this->tpl_var['catids'][$key]['catid']; ?>" class="badge pull-right">更多 <em class="glyphicon glyphicon-plus"></em> </a>
						</h2>
						<ul class="list-unstyled list-img">
                            <?php $cid = 0;
 foreach($contents['data'] as $key => $content){ 
 $cid++; ?>
							<li class="border morepadding">
								<h4 class="shorttitle">
									<a href="index.php?content-app-content&contentid=<?php echo $content['contentid']; ?>">
									<?php echo $content['contenttitle']; ?>
									</a>
								</h4>
								<div class="intro">
									<div class="col-xs-3 img">
										<a href="index.php?content-app-content&contentid=<?php echo $content['contentid']; ?>">
											<img src="<?php echo $content['contentthumb']; ?>" />
										</a>
									</div>
									<div class="desc">
										<p><?php echo $content['contentdescribe']; ?></p>
										<p class="toolbar">
                                            <?php echo date('Y-m-d',$content['contentinputtime']); ?>
											【 <?php echo $this->tpl_var['categories'][$content['contentcatid']]['catname']; ?> 】
											<a href="index.php?content-app-content&contentid=<?php echo $content['contentid']; ?>" class="btn btn-info pull-right more">查看详情</a>
										</p>
									</div>
								</div>
							</li>
                            <?php } ?>
						</ul>
					</div>
                    <?php } ?>
				</div>
				<div class="col-xs-3 nopadding">
					<div class="content-box padding">
						<h2 class="title">推荐专题</h2>
						<ul class="list-unstyled list-txt">
                            <?php $sid = 0;
 foreach($this->tpl_var['topseminars']['data'] as $key => $seminar){ 
 $sid++; ?>
							<li class="striped">
								<a href="index.php?seminar-app-seminar&seminarid=<?php echo $seminar['pccontentid']; ?>"> <?php echo $seminar['pctitle']; ?></a>
							</li>
                            <?php } ?>
						</ul>
					</div>
				</div>
			</div>
            <?php $this->_compileInclude('footer'); ?>
		</div>
	</div>
</div>
</body>
</html>