<?php

/*********************************************************************
    index.php

    Helpdesk landing page. Please customize it to fit your needs.

    Peter Rotich <peter@osticket.com>
    Copyright (c)  2006-2013 osTicket
    http://www.osticket.com

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
 **********************************************************************/
require('client.inc.php');

require_once INCLUDE_DIR . 'class.page.php';

$section = 'home';
require(CLIENTINC_DIR . 'header.inc.php');
?>
<div id="landing_page">

    <div class="main-content">

        <div class="thread_body welcome_post">
            <?php
            if ($cfg && ($page = $cfg->getLandingPage()))
                echo $page->getBodyWithImages();
            else
                echo  '<h1>' . __('Welcome to the Support Center') . '</h1>';
            ?>
        </div>
        <div>
            <?php
            $BUTTONS = isset($BUTTONS) ? $BUTTONS : true;
            ?>
            <?php if ($BUTTONS) { ?>
                <div class="front-page-buttons">
                    <?php
                    if (
                        $cfg->getClientRegistrationMode() != 'disabled'
                        || !$cfg->isClientLoginRequired()
                    ) { ?>
                        <a href="open.php" style="display:block" class="blue button"><?php echo __('Open a New Ticket'); ?></a>

                    <?php } ?>
                    <a href="view.php" style="display:block" class="green button"><?php echo __('Check Ticket Status'); ?></a>
                </div>
            <?php } ?>
        </div>
    </div>

    <?php
    if ($cfg && $cfg->isKnowledgebaseEnabled()) { ?>
        <div class="kb_details">
            <div class="featured-category-cover">
                <?php
                $cats = Category::getFeatured();
                if ($cats->all()) { ?>
                    <h1><?php echo __('Featured Knowledge Base Articles'); ?></h1>
                <?php
                }
                foreach ($cats as $C) { ?>
                    <div class="featured-category front-page">
                        <div class="featured-category-header">
                            <i class="icon-folder-open"></i>
                            <div class="category-name">
                                <?php echo $C->getName(); ?>
                            </div>
                        </div>
                        <?php foreach ($C->getTopArticles() as $F) { ?>
                            <div class="article-headline">
                                <div class="article-title">
                                    <a href="<?php echo ROOT_PATH; ?>kb/faq.php?id=<?php echo $F->getId(); ?>">
                                        <?php echo $F->getQuestion(); ?>
                                    </a>
                                </div>
                                <div class="article-teaser"><?php echo $F->getTeaser(); ?></div>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
            <div>
                <div class="search-form">
                    <form method="get" action="kb/faq.php">
                        <input type="hidden" name="a" value="search" />
                        <input type="text" name="q" class="search" placeholder="<?php echo __('Search our knowledge base'); ?>" />
                        <button type="submit" class="button"><?php echo __('Search'); ?></button>
                    </form>
                </div>

                <?php
                if (($faqs = FAQ::getFeatured()->select_related('category')->limit(5))
                    && $faqs->all()
                ) { ?>
                    <section class="side-widget">
                        <div class="header"><?php echo __('Featured Questions'); ?></div>
                        <div class="side-widget-entry">
                            <?php foreach ($faqs as $F) { ?>
                                <div>
                                    <a href="<?php echo ROOT_PATH; ?>kb/faq.php?id=<?php echo urlencode($F->getId()); ?>">
                                        <?php echo $F->getLocalQuestion(); ?>
                                    </a>
                                </div>
                            <?php } ?>
                        </div>
                    </section>
                <?php
                }
                $resources = Page::getActivePages()->filter(array('type' => 'other'));
                if ($resources->all()) { ?>
                    <section class="side-widget">
                        <div class="header"><?php echo __('Other Resources'); ?></div>
                        <div class="side-widget-entry">
                            <?php foreach ($resources as $page) { ?>
                                <div><a href="<?php echo ROOT_PATH; ?>pages/<?php echo $page->getNameAsSlug(); ?>"><?php echo $page->getLocalName(); ?></a></div>
                            <?php } ?>
                        </div>
                    </section>
                <?php } ?>
            </div>
        </div>
    <?php } ?>
</div>

<?php require(CLIENTINC_DIR . 'footer.inc.php'); ?>