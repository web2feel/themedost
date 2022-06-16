 <div class="row">
     <div class="span8">
         <?php
            $categories = Category::objects()
                ->exclude(Q::any(array(
                    'ispublic' => Category::VISIBILITY_PRIVATE,
                    Q::all(array(
                        'faqs__ispublished' => FAQ::VISIBILITY_PRIVATE,
                        'children__ispublic' => Category::VISIBILITY_PRIVATE,
                        'children__faqs__ispublished' => FAQ::VISIBILITY_PRIVATE,
                    ))
                )))
                //->annotate(array('faq_count'=>SqlAggregate::COUNT('faqs__ispublished')));
                ->annotate(array('faq_count' => SqlAggregate::COUNT(
                    SqlCase::N()
                        ->when(array(
                            'faqs__ispublished__gt' => FAQ::VISIBILITY_PRIVATE
                        ), 1)
                        ->otherwise(null)
                )))
                ->annotate(array('children_faq_count' => SqlAggregate::COUNT(
                    SqlCase::N()
                        ->when(array(
                            'children__faqs__ispublished__gt' => FAQ::VISIBILITY_PRIVATE
                        ), 1)
                        ->otherwise(null)
                )));

            // ->filter(array('faq_count__gt' => 0));
            if ($categories->exists(true)) { ?>
             <div class="kb-header">
                 <h2> <?php echo __('Click on the category to browse FAQs.'); ?> </h2>

             </div>
             <ul id="kb">
                 <?php
                    foreach ($categories as $C) {
                        // Don't show subcategories with parents.
                        if (($p = $C->parent)
                            && ($categories->findFirst(array(
                                'category_id' => $p->getId()
                            )))
                        )
                            continue;

                        $count = $C->faq_count + $C->children_faq_count;
                    ?>
                     <li>

                         <div>
                             <h4> <i class="icon-folder-open"></i> <?php echo sprintf(
                                                                        '<a href="faq.php?cid=%d">%s %s</a>',
                                                                        $C->getId(),
                                                                        Format::htmlchars($C->getLocalName()),
                                                                        $count ? "({$count})" : ''
                                                                    ); ?></h4>
                             <div class="faded">
                                 <?php echo Format::safe_html($C->getLocalDescriptionWithImages()); ?>
                             </div>
                             <?php
                                if (($subs = $C->getPublicSubCategories())) {
                                    echo '<p/><div style="padding:10px 0px 10px 30px;">';
                                    foreach ($subs as $c) {
                                        echo sprintf(
                                            '<div style="font-weight:bold"><i class="icon-folder-open"></i>
                            <a href="faq.php?cid=%d">%s (%d)</a></div>',
                                            $c->getId(),
                                            $c->getLocalName(),
                                            $c->faq_count
                                        );
                                    }
                                    echo '</div>';
                                } ?>
                             <div class="popular-faqs">
                                 <?php foreach ($C->faqs
                                        ->exclude(array('ispublished' => FAQ::VISIBILITY_PRIVATE))
                                        ->limit(5) as $F) { ?>
                                     <div class="popular-faq"><i class="icon-file-alt"></i>
                                         <a href="faq.php?id=<?php echo $F->getId(); ?>">
                                             <?php echo $F->getLocalQuestion() ?: $F->getQuestion(); ?>
                                         </a>
                                     </div>
                                 <?php } ?>
                             </div>
                         </div>
                     </li>
                 <?php   } ?>
             </ul>
         <?php
            } else {
                echo __('NO FAQs found');
            }
            ?>
     </div>

     <div class="span4">
         <div class="sidebar">
             <div class="searchbar">
                 <form method="get" action="faq.php">
                     <input type="hidden" name="a" value="search" />
                     <input type="text" name="q" class="search" placeholder="<?php
                                                                                echo __('Search our knowledge base'); ?>" />
                     <input type="submit" style="display:none" value="search" />
                 </form>
             </div>

             <div class="searchbar">
                 <form method="get" action="faq.php">
                     <input type="hidden" name="a" value="search" />
                     <select name="topicId" style="width:100%;max-width:100%" onchange="javascript:this.form.submit();">
                         <option value="">—<?php echo __("Browse by Topic"); ?>—</option>
                         <?php
                            $topics = Topic::objects()
                                ->annotate(array('has_faqs' => SqlAggregate::COUNT('faqs')))
                                ->filter(array('has_faqs__gt' => 0));
                            foreach ($topics as $T) { ?>
                             <option value="<?php echo $T->getId(); ?>"><?php echo $T->getFullName(); ?></option>
                         <?php } ?>
                     </select>
                 </form>
             </div>
         </div>
     </div>

 </div>