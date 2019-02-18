<div class="reviews2">
<?php if ($reviews) { ?>
<?php foreach ($reviews as $review) { ?>
<div class="query-box">
						<h5><?php echo $review['author']; ?></b>  </h5>
						<ul class="review-stars clearfix">
						<?php
							for($i=1;$i<=$review['rating'];$i++)
							{
								?>
								<li class="fill"><a href="#"><i class="fa fa-star"></i></a></li>
								<?php
							}
							?>
							<?php
							for($j=$i;$j>$i;$j--)
							{
								?>
								<li><a href="javascript:void(0)"><i class="fa fa-star"></i></a></li>
								<?php
							}
							?>
						</ul>
						<p><?php echo $review['text'];?></p>
						<!-- <a href="#comment-pop" class="btn btn-primary fancybox">Comment</a> -->
					</div>
<?php } ?>
</div>
<a href="javascript:void" data-page="1" class="viewmore">View more Reviews</a>
<?php } else { ?>
<div class="content"><?php echo $text_no_reviews; ?></div>
<?php } ?>

