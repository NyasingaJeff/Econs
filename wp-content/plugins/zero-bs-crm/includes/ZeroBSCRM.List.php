<?php 
/*!
 * Jetpack CRM
 * https://jetpackcrm.com
 * V1.0
 *
 * Copyright 2020 Aut O’Mattic
 *
 * Date: 26/05/16
 */

/* ======================================================
  Breaking Checks ( stops direct access )
   ====================================================== */
    if ( ! defined( 'ZEROBSCRM_PATH' ) ) exit;
/* ======================================================
  / Breaking Checks
   ====================================================== */

class zeroBSCRM_list{

    private $objType = false;
    private $objTypeID = false; // Will be set in v3.0+ - is autogenned from $objType ^^
    private $singular = false;
    private $plural = false;
    private $tag = false;
    private $postType = false;
    private $postPage = false;
    private $langLabels = false;
    private $bulkActions = false;
    private $sortables = false;
    private $unsortables = false;
    private $extraBoxes = '';
    private $extraJS = '';
    private $messages = false;
        #} All messages need params to match this func: 
        #} ... zeroBSCRM_UI2_messageHTML($msgClass='',$msgHeader='',$msg='',$iconClass='',$id='')

    function __construct($args=array()) {

        #} =========== LOAD ARGS ==============
        $defaultArgs = array(

            'objType'   => false,   //transaction
            'singular'   => false,  //Transaction
            'plural' => false,      //Transactions
            'tag' => false,         //zerobs_transactiontag
            'postType' => false,    //zerobs_transaction
            'postPage' => false,    //manage-transactions
            'langLabels' => array(
                    
                    // bulk actions - general
                    'view' => __('View',"zero-bs-crm"),
                    'edit' => __('Edit',"zero-bs-crm"),
                    'deletestr' => __('Delete',"zero-bs-crm"),
                    'nocustomer' => __('Not Assigned',"zero-bs-crm"),
                    'areyousure' => __('Are you sure?',"zero-bs-crm"),
                    'acceptyesdoit' => __('Yes, accept',"zero-bs-crm"),
                    'yesproceed'  => __('Yes, proceed',"zero-bs-crm"),
                    'changestatus' => __('Change Status',"zero-bs-crm"),
                    'yesupdate' => __('Yes, update',"zero-bs-crm"),
                    
                    // bulk actions - delete
                    'areyousurethese' => __('Are you sure you want to delete these?',"zero-bs-crm"),
                    'yesdelete' => __('Yes, delete!','zero-bs-crm'),
                    'noleave' => __('No, leave them',"zero-bs-crm"),
                    'yesthose' => __('Yes, remove everything',"zero-bs-crm"),
                    'deleted' => __('Deleted',"zero-bs-crm"),
                    'notdeleted' => __('Could not delete!',"zero-bs-crm"),

                    // tag related
                    'addtags' => __('Add tag(s)',"zero-bs-crm"),
                    'addtags' => __('Add tags',"zero-bs-crm"),
                    'removetags' => __('Remove tag(s)',"zero-bs-crm"),
                    'addthesetags' => __('Add Tags',"zero-bs-crm"),
                    'whichtags' => __('Which Tag(s)?',"zero-bs-crm"),
                    'whichtagsadd' => __('Which Tag(s) would you like to add?',"zero-bs-crm"),
                    'whichtagsremove' => __('Which Tag(s) would you like to remove?',"zero-bs-crm"),                    
                    'addthesetags' => __('Add Tags',"zero-bs-crm"),
                    'tagsadded' => __('Tags Added',"zero-bs-crm"),
                    'tagsaddeddesc' => __('Your tags have been successsfully added.',"zero-bs-crm"),
                    'tagsnotadded' => __('Tags Not Added',"zero-bs-crm"),
                    'tagsnotaddeddesc' => __('Your tags could not be added.',"zero-bs-crm"),
                    'tagsnotselected' => __('No Tags Selected',"zero-bs-crm"),
                    'tagsnotselecteddesc' => __('You did not select any tags.',"zero-bs-crm"),
                    'removethesetags' => __('Remove Tags',"zero-bs-crm"),
                    'tagsremoved' => __('Tags Removed',"zero-bs-crm"),
                    'tagsremoveddesc' => __('Your tags have been successsfully removed.',"zero-bs-crm"),
                    'tagsnotremoved' => __('Tags Not Removed',"zero-bs-crm"),
                    'tagsnotremoveddesc' => __('Your tags could not be removed.',"zero-bs-crm"),
                    'notags' => __('You do not have any tags',"zero-bs-crm"),
               

                    // bulk actions - merge 2 records
                    'merged' => __('Merged',"zero-bs-crm"),
                    'notmerged' => __('Not Merged',"zero-bs-crm"),
                    'yesmerge' => __('Yes, merge them',"zero-bs-crm"),

            ),
            'bulkActions' => array(),
            'sortables' => array('id'),
            'unsortables' => array('tagged','latestlog','editlink','phonelink'),
            'extraBoxes' => '', // html for extra boxes e.g. upsells :)
            'extraJS' => '',
            'messages' => '',

            //not implemented 'hideSidebar' => false // ability to hard-hide sidebar

        ); foreach ($defaultArgs as $argK => $argV){ $this->$argK = $argV; if (is_array($args) && isset($args[$argK])) {  if (is_array($args[$argK])){ $newData = $this->$argK; if (!is_array($newData)) $newData = array(); foreach ($args[$argK] as $subK => $subV){ $newData[$subK] = $subV; }$this->$argK = $newData;} else { $this->$argK = $args[$argK]; } } }
        #} =========== / LOAD ARGS =============

        global $zbs;

        // if not DAL3, this has to be contact:
        if ($this->objTypeID == false){

            if ($zbs->isDAL3()) // translate it from 'transaction' in objType
                $this->objTypeID = $zbs->DAL->objTypeID($this->objType);
            else
                $this->objTypeID = ZBS_TYPE_CONTACT;

        }

    }

    public function drawListView(){

        if (empty($this->objType) || empty($this->postType) || empty($this->postPage) || empty($this->singular) || empty($this->plural)){

            return 'Error.';
        }

        global $zbs;


        #} Retrieve all passed filters (tags, etc.)
        $listViewFilters = array(); if (isset($_GET['zbs_tag'])){

            $possibleTag = (int)sanitize_text_field($_GET['zbs_tag']);
            // deal with tags diff in DAL2+ // DAL/2+ switch:
            if (
                // DAL2 has tags only for contacts
                $zbs->isDAL2() && $this->tag == 'zerobscrm_customertag'
                ||
                // DAL3 has them for everything with a list view :D
                $zbs->isDAL3()
                ){

                $possibleTagObj = $zbs->DAL->getTag($possibleTag,array('objtype'=>$this->objTypeID));

                if (isset($possibleTagObj['id'])){

                    $listViewFilters['tags'] = array($possibleTagObj);

                }


            } else {

                $possibleTagObj = get_term_by('id',$possibleTag,$this->tag);

                if (isset($possibleTagObj->term_id)){

                    $listViewFilters['tags'] = array($possibleTagObj);

                }
            }

        }
        if (isset($_GET['s']) && !empty($_GET['s'])){

            $listViewFilters['s'] = sanitize_text_field($_GET['s']);

        }
        if (isset($_GET['quickfilters']) && !empty($_GET['quickfilters'])){

            // set it whether legit? what'll this do on error urls people make up?
            // v2.2+ hone this + add multi-filter
            // v2.99.5 - ALWAYS lowercase :) 
            $possibleQuickFilters = strtolower(sanitize_text_field($_GET['quickfilters']));
            $listViewFilters['quickfilters'] = array($possibleQuickFilters);

        }


        #} Paging
        $currentPage = 1; if (isset($_GET['paged'])) $currentPage = (int)sanitize_text_field($_GET['paged']);

        #} Sort
        $sort = false; if (isset($_GET['sort']) && !empty($_GET['sort'])) $sort = sanitize_text_field($_GET['sort']);
        $sortOrder = false; if (isset($_GET['sortdirection']) && ($_GET['sortdirection'] == 'asc' || $_GET['sortdirection'] == 'desc')) $sortOrder = sanitize_text_field($_GET['sortdirection']);


        # SCAFFOLDING - TO BE RE-ARRANGED :) 
        #} NOTE SECOND FIELD IN THESE ARE NOW IGNORED!?!? (30/7)

            #} Centralised into ZeroBSCRM.List.Columns.php 30/7/17
            $columnVar = 'zeroBSCRM_columns_'.$this->objType; //$zeroBSCRM_columns_transaction;
            global $$columnVar;
            $defaultColumns = ${$columnVar}['default'];
            $allColumns = ${$columnVar}['all'];


        global $zbs;
        $usingOwnership = $zbs->settings->get('perusercustomers');

        #} Retrieve columns settings
        $customViews = $zbs->settings->get('customviews2');

        $currentColumns = false; if (isset($customViews) && isset($customViews[$this->objType])) $currentColumns = $customViews[$this->objType];
        if ($currentColumns == false) $currentColumns = $defaultColumns;


        #} Filter buttons
        // load defaults (List.columns.php)
        $filterVar = 'zeroBSCRM_filterbuttons_'.$this->objType; //$zeroBSCRM_filterbuttons_transaction;
        global $$filterVar;
        $defaultFilterButtons = ${$filterVar}['default'];
        // retrieve from customViews (as retrieved above)
        $currentFilterButtons = false; if (isset($customViews) && isset($customViews[$this->objType.'_filters'])) $currentFilterButtons = $customViews[$this->objType.'_filters'];
        if ($currentFilterButtons == false) $currentFilterButtons = $defaultFilterButtons;
        $allFilterButtons = ${$filterVar}['all'];



        // if contact on db2 - try everything sortable:
        if ($zbs->isDAL2() && $this->objType == 'customer'){

            // add all columns to sortables :)
            foreach ($currentColumns as $col => $var){
                if (!in_array($col, $this->unsortables) && !in_array($col,$this->sortables)) $this->sortables[] = $col;
            }

        }

        //print_r($allColumns); exit();
        //print_r($currentColumns); exit();
        //print_r($this->sortables); exit();



        #} Refresh 2
        ?>
        <style>
            #zbs-toggle-sidebar{
                background:white !important;
            }
        </style>


        <div class="zbs-semantic wrap">
            <!-- title + edit ico -->



        </div>

            <!-- col editor -->
            <div id="zbs-list-col-editor" class="ui segment secondary hidden">            

                <h4 class="ui horizontal divider header">
                  <i class="list layout icon"></i>
                  <?php _e($this->singular.' List View Options',"zero-bs-crm"); ?>
                </h4>
    
                <?php if (zeroBSCRM_isZBSAdminOrAdmin()){ // only admin can manage columns (globally) ?>
                <div id="zbs-list-view-options-wrap" class="ui divided grid">

                  <div class="ui active inverted dimmer hidden" id="zbs-col-manager-loading" style="display:none">
                    <div class="ui text loader"><?php _e('Loading',"zero-bs-crm");?></div>
                  </div>

                    <div class="row">
                        <div class="ten wide column">

                            <h4><?php _e('Current Columns',"zero-bs-crm"); ?></h4>


                            <div id="zbs-column-manager-current-cols" class="ui segment zbs-column-manager-connected"> 
                                <?php if (is_array($currentColumns)) foreach ($currentColumns as $colKey => $col){

                                    ?><div id="zbs-column-manager-col-<?php echo $colKey; ?>" class="ui compact tiny basic button zbs-column-manager-col" data-key="<?php echo $colKey; ?>"><?php _e($col[0],"zero-bs-crm"); ?></div><?php

                                } ?>
                            </div>

                        </div>
                        <div class="six wide column">

                            <h4><?php _e('Available Columns',"zero-bs-crm"); ?></h4>

                            <div id="zbs-column-manager-available-cols" class="ui segment"> 
                                <?php if (is_array($allColumns)) {

                                    // here we split them into groups, where there is. This allows a seperation of 'base fields' and compute fields (e.g. total value)
                                    $allColumnsSorted = array('basefields'=>array(),'other'=>array());
                                    $hasMultiColumnGroups = 0;

                                    foreach ($allColumns as $colKey => $col){

                                        if (!array_key_exists($colKey, $currentColumns)){

                                            // split em up
                                            if (isset($col[2]) && $col[2] == 'basefield'){
                                                $allColumnsSorted['basefields'][$colKey] = $col;
                                                $hasMultiColumnGroups = true;
                                            } else
                                                $allColumnsSorted['other'][$colKey] = $col;

                                        }

                                    }

                                    // now we put them out sequentially
                                    $colGroupCount = 0;
                                    foreach ($allColumnsSorted as $sortGroup => $columns){

                                        if (is_array($columns) && count($columns) > 0){

                                            // put out a grouper + title
                                            echo '<div>';

                                            if ($hasMultiColumnGroups){

                                                // header - <i class="list layout icon"></i>

                                                $title = ''; $extraStyles = '';
                                                switch ($sortGroup){

                                                    case 'basefields':
                                                        $title = __('Fields','zero-bs-crm');
                                                        break;

                                                    default: 
                                                        $title = __('Extra Fields','zero-bs-crm');
                                                        break;
                                                }

                                                if ($colGroupCount > 0) $extraStyles = 'margin-top: 1em;';

                                                if (!empty($title)) echo '<h4 class="ui horizontal divider header" style="'.$extraStyles.'">'.$title.'</h4>';
                                            }

                                            echo '<div class="zbs-column-manager-connected">';

                                            foreach ($columns as $colKey => $col){

                                                if (!array_key_exists($colKey, $currentColumns)){
                                                    
                                                    ?><div id="zbs-column-manager-col-<?php echo $colKey; ?>" class="ui compact tiny basic button zbs-column-manager-col" data-key="<?php echo $colKey; ?>"><?php _e($col[0],"zero-bs-crm"); ?></div><?php

                                                }

                                            }

                                            echo '</div></div>';

                                            $colGroupCount++;
                                        }

                                    }

                                    // if NONE output, we need to always have smt to drop to, so put empty:
                                    if ($colGroupCount == 0){
                                        echo '<div class="zbs-column-manager-connected">';
                                        echo '</div>';
                                    }


                                } ?>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="ui divider"></div>
                <?php } // if admin/can manage columns ?>

                <div id="zbs-list-options-base-wrap" class="ui grid">

                    <?php 
                        # here we add stuff which is saved by screenOptions, even tho it's in its own dom elements, not sceen options area 
                        $screenOpts = $zbs->userScreenOptions();

                        // debug echo '<pre>'; print_r($screenOpts); echo '</pre>'; 

                        // default
                        $perPage = 20; 
                        if (isset($screenOpts) && is_array($screenOpts) && isset($screenOpts['perpage']) && !empty($screenOpts['perpage']) && $screenOpts['perpage'] > 0) $perPage = (int)$screenOpts['perpage'];
                    ?>
                    <div class="two column clearing centered row">

                        <div class="column" style="max-width:364px;">
                            <div class="ui labeled input">
                                <div class="ui teal label"><i class="table icon"></i>  <?php _e('Records per page:','zero-bs-crm'); ?></div>
                                <input type="text" style="width:70px;" class="intOnly" id="zbs-screenoptions-records-per-page" value="<?php echo $perPage; ?>" />
                            </div>
                        </div>                    

                    </div>


                    <?php /* don't show for now
                    if ($usingOwnership){ ?>
                    <div class="two column clearing centered row">

                        <div class="column" style="max-width:364px;">
                                <div class="ui form zbs-list-toolbar-radio">
                                  <div class="inline fields">
                                    <label><?php _e('Show (Assigned to)',"zero-bs-crm");?>:</label>
                                    <div class="field">
                                      <div class="ui radio checkbox">
                                        <input type="radio" checked="checked" name="zbs-show-customers-via-ownership" value="all" id="zbs-list-toolbar-show-all" tabindex="0" class="hidden">
                                        <label><?php _e('All',"zero-bs-crm");?></label>
                                      </div>
                                    </div>
                                    <div class="field">
                                      <div class="ui radio checkbox">
                                        <input type="radio" name="zbs-show-customers-via-ownership" value="mine" id="zbs-list-toolbar-show-mine" tabindex="0" class="hidden">
                                        <label><?php _e('Mine',"zero-bs-crm");?></label>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                        </div>                    

                    </div> */ ?>


                <div class="ui divider" style="margin-bottom:0;margin-top:0;"></div>

                    <div class="two column clearing centered row">

                        <div class="column" style="max-width:364px;">
                            <button id="zbs-columnmanager-bottomsave" type="button" class="ui button positive"><i class="check square icon"></i> <?php _e('Save Options and Close','zero-bs-crm'); ?></button>
                        </div>                    

                    </div>

                </div>


            </div>

            <!-- main view: segments -->
            <div id="zbs-list-segments" style="display:none"></div>

            <div id="zbs-list-warnings-wrap">
                <?php #} Pre-loaded msgs, because I wrote the helpers in php first... should move helpers to js and fly these 

                echo zeroBSCRM_UI2_messageHTML('warning hidden','Error Retrieving '.$this->plural,'There has been a problem retrieving your '.$this->plural.', if this issue persists, please contact support.','disabled warning sign','zbsCantLoadData');
                echo zeroBSCRM_UI2_messageHTML('warning hidden','Error Updating Columns '.$this->plural,'There has been a problem saving your column configuration, if this issue persists, please contact support.','disabled warning sign','zbsCantSaveCols');
                echo zeroBSCRM_UI2_messageHTML('warning hidden','Error Updating Columns '.$this->plural,'There has been a problem saving your filter button configuration, if this issue persists, please contact support.','disabled warning sign','zbsCantSaveButtons');
                echo zeroBSCRM_UI2_messageHTML('info hidden',__('No '.$this->plural.' Found',"zero-bs-crm"),__('There are no '.$this->plural.' here, do you want to',"zero-bs-crm").' <a href="'.zbsLink('create',-1,$this->postType).'" class="">'.__('Create one',"zero-bs-crm").'</a>?','disabled warning sign','zbsNoResults');

                // any additional messages?
                if (isset($this->messages) && is_array($this->messages) && count($this->messages) > 0){

                    //echo '<div id="zbs-list-view-messages">';

                        foreach ($this->messages as $message){
                            // $message needs to match this func :)
                            echo zeroBSCRM_UI2_messageHTML($message[0],$message[1],$message[2],$message[3],$message[4]);
                        }

                    //echo '</div>';

                }

                ?>
            </div>
            <!-- main view: list + sidebar -->
            <div id="zbs-list-wrap" class="ui divided grid zbs-list-view-<?php echo $this->objType; ?>">

                <div class="row">


                    <!-- record list -->
                    <div class="twelve wide column" id="zbs-list-table-wrap">
                        <?php #} Drawn by Javascript :) ?>
                    </div>
                    <!-- side bar -->
                    <div class="four wide column" id="zbs-list-sidebar-wrap">

                        <!-- search box -->
                        <div class="">
                            <div class="ui fluid action input">
                                <input type="text" name="s" id="zbs-listview-search" placeholder="<?php _e('Search...',"zero-bs-crm"); ?>">
                                <button class="ui icon button green" id="zbs-listview-runsearch">
                                    <i class="search icon"></i>
                                </button>
                            </div>
                        </div>
                        <!-- / search box -->

                        <?php #got filters?
                        if (is_array($allFilterButtons) && count($allFilterButtons) > 0){ ?>
                        <div class="ui divider"></div>

                        <!-- Filters box -->
                        <div class="">
                            <?php if (current_user_can('administrator')){ ?>
                                <button class="ui right floated compact icon button zbs-list-view-edit-button" id="zbs-list-view-edit-filters"><i class="options icon"></i></button>
                            <?php } ?>
                            <h4><span id="zbs-list-filters-edit-title" class="hidden"><?php _e('Edit',"zero-bs-crm");?> </span><?php _e("Filters","zero-bs-crm");?></h4>
                    
                            <div id="zbs-list-filters"><?php

                            /* just let the js draw these actually... else not DRY (maintaining 2 funcs php + js)
                                if (is_array($currentFilterButtons)) foreach ($currentFilterButtons as $buttonKey => $button){
                                        
                                    $zbsurl = get_admin_url('','edit.php?post_type=zerobs_customer&page=manage-customers') ."&quickfilters=".$buttonKey;
                                    ?><a href="<?php echo $zbsurl; ?>" class="ui olive button tiny"><?php echo $button[0]; ?></a><?php

                                }
                                */

                            ?>
                            </div>

                            <?php if (current_user_can('administrator') && is_array($allFilterButtons) && count($allFilterButtons) > 0){ ?>
                            <!-- edit box -->
                            <div id="zbs-list-view-edit-filters-wrap" class="hidden">

                                <div class="ui active inverted dimmer hidden" id="zbs-filter-button-manager-loading" style="display:none">
                                    <div class="ui text loader"><?php _e("Loading","zero-bs-crm");?></div>
                                  </div>

                                <div  class="ui segment">

                                    <h5><?php _e('Current Filters',"zero-bs-crm"); ?></h5>

                                    <div id="zbs-list-view-filter-options-current" class="zbs-filter-manager-connected ui-sortable">

                                    <?php if (is_array($currentFilterButtons)) foreach ($currentFilterButtons as $filterButtonKey => $filterButton){

                                        ?><div id="zbs-filter-manager-button-<?php echo $filterButtonKey; ?>" class="ui basic button tiny zbs-filter-button-manager-button" data-key="<?php echo $filterButtonKey; ?>"><?php echo $filterButton[0]; ?></div><?php

                                    } ?>


                                    </div>

                                </div>

                                <div class="ui segment">

                                    <h5><?php _e('Available Filters',"zero-bs-crm"); ?></h5>

                                    <div id="zbs-list-view-filter-options-available" class="zbs-filter-manager-connected ui-sortable">

                                    <?php foreach ($allFilterButtons as $filterButtonKey => $filterButton){

                                                if (!array_key_exists($filterButtonKey, $currentFilterButtons)){

                                                    ?><div id="zbs-filter-manager-button-<?php echo $filterButtonKey; ?>" class="ui basic button tiny zbs-filter-button-manager-button" data-key="<?php echo $filterButtonKey; ?>"><?php echo $filterButton[0]; ?></div><?php

                                                }

                                    } ?>

                                    </div>

                                </div>

                            </div><?php } ?>



                        </div>
                        <!-- / Filters box --><?php } ?>

                        
                        <?php 

                        // DAL/2+ switch:
                        if (
                            // DAL2 has tags only for contacts
                            $zbs->isDAL2() && $this->tag == 'zerobscrm_customertag'
                            ||
                            // DAL3 has them for everything with a list view :D
                            $zbs->isDAL3()
                            ){

                            // DAL2+
                            $terms = $zbs->DAL->getTagsForObjType(array(
                                    'objtypeid'=>$this->objTypeID,
                                    'withCount'=>true,
                                    // we need empty for bulk, which also uses this list, so don't exclude empty '
                                    // 'excludeEmpty'=>true,
                                    // but do in ui below :)
                                    'excludeEmpty' => false,
                                    'ignoreowner' => true));                          

                            if (is_array($terms) && count($terms) > 0){ ?>
                            <div class="ui divider"></div>
                            <!-- Tagged box -->
                                <div class="">
                                    
                                    <h4><?php _e("Tagged","zero-bs-crm"); ?></h4>
                            
                                    <div id="zbs-list-tags">
                                        <?php 
                                        
                                        $tagIndex = array();
                                        
                                        // inefficient - but hacky
                                        if (isset($listViewFilters['tags']) && is_array($listViewFilters['tags']) && count($listViewFilters['tags']) > 0){

                                            foreach ($listViewFilters['tags'] as $tag){
                                                $tagIndex[] = $tag['id'];
                                            }
                                        }

                                        // if over 12 tags, use mini
                                        $tagClass = 'tiny'; if (count($terms) > 12) $tagClass = 'mini';

                                            // zbs_prettyprint($terms);
                                            $i = 1;
                                            $total_tags = count($terms);
                                            foreach($terms as $term){

                                                // exclude empties here :) 
                                                if ($term['count'] > 0){

                                                    $zbsurl = zbsLink('listtagged',-1,$this->objTypeID,-1,$term['id']);//get_admin_url('','admin.php?page='.$this->postPage ."&zbs_tag=".$term['id']);
                                                    $zbstermc = zeroBSCRM_prettifyLongInts($term['count']);
                                                    if($i==1){
                                                        echo "<div class='first-ten-tags'>";
                                                    }
                                                    // check
                                                    if (in_array($term['id'], $tagIndex))
                                                        $tagColor = 'blue';
                                                    else
                                                        $tagColor = 'teal';

                                                    // handle super long tag names

                                                    echo '<a href="'.$zbsurl.'" class="ui button '.$tagClass.' '.$tagColor.'">'. $term['name'] . " (<span class='sub-count'>" .$zbstermc. "</span>)</a>";
                                                

                                                    if($i == 6 && $total_tags > 6){
                                                        echo "</div>"; //end first 10 tags
                                                            #} tags UI for showing all
                                                            echo "<div class='show-more-tags ui button olive tiny'>";
                                                                _e("Show all tags","zero-bs-crm");
                                                            echo "</div>";
                                                        echo "<div class='more-tags'>";
                                                    }

                                                    $i++;

                                                } // / if not zero count

                                            }
                                            if($i >= 6 && $total_tags > 6){
                                                echo "</div>"; //close the more tags
                                            }
                                        ?>

                                    </div>

                                </div>
                            <!-- / Tagged box -->
                            <?php } 

                        


                        } else {

                            // DAL1

                            // get tags ahead of time + only show if not empty :)
                            if (!empty($this->tag)) {
                                    $terms = get_terms( $this->tag, array(
                                    'hide_empty' => false,
                                    'orderby'    => 'count',
                                    'order' => 'ASC'
                                ) );
                            } else $terms = array();

                            if (is_array($terms) && count($terms) > 0){ ?>
                            <div class="ui divider"></div>
                            <!-- Tagged box -->
                                <div class="">
                                    
                                    <h4><?php _e("Tagged","zero-bs-crm");?></h4>
                            
                                    <div id="zbs-list-tags">
                                        <?php 
                                        
                                        $tagIndex = array();
                                        
                                        // inefficient - but hacky
                                        if (isset($listViewFilters['tags']) && is_array($listViewFilters['tags']) && count($listViewFilters['tags']) > 0){

                                            foreach ($listViewFilters['tags'] as $tag){
                                                $tagIndex[] = $tag->term_id;
                                            }
                                        }

                                            // zbs_prettyprint($terms);
                                            $i = 1;
                                            foreach($terms as $term){
                                                $zbsurl = get_admin_url('','edit.php?post_type='.$this->postType.'&page='.$this->postPage) ."&zbs_tag=".$term->term_id;
                                                $zbstermc = zeroBSCRM_prettifyLongInts($term->count);
                                                if($i==1){
                                                    echo "<div class='first-ten-tags'>";
                                                }
                                                // check
                                                if (in_array($term->term_id, $tagIndex))
                                                    $tagColor = 'blue';
                                                else
                                                    $tagColor = 'teal';

                                                // handle super long tag names

                                                echo '<a href="'.$zbsurl.'" class="ui button tiny '.$tagColor.'">'. $term->name . " (<span class='sub-count'>" .$zbstermc. "</span>)</a>";
                                            

                                                if($i == 6){
                                                    echo "</div>"; //end first 10 tags
                                                        #} tags UI for showing all
                                                        echo "<div class='show-more-tags ui button olive tiny'>";
                                                            _e("Show all tags","zero-bs-crm");
                                                        echo "</div>";
                                                    echo "<div class='more-tags'>";
                                                }

                                                $i++;

                                            }
                                            if($i >= 6){
                                                echo "</div>"; //close the more tags
                                            }
                                        ?>

                                    </div>

                                </div>
                            <!-- / Tagged box -->
                            <?php } 

                        } // DAL 1 ?>

                        <div class="ui divider"></div>
                        <?php ##WLREMOVE ?>
                        <?php echo $this->extraBoxes; ?>
                        <?php ##/WLREMOVE ?>
                    </div>
                </div>

                <!-- could use this for mobile variant?) 
                <div class="two column mobile only row" style="display:none"></div>
                -->
            </div> <!-- / mainlistview wrap -->
        </div>

        <script type="text/javascript">

            // expose log types (For columns)
            var zbsLogTypes = <?php global $zeroBSCRM_logTypes; echo json_encode($zeroBSCRM_logTypes); ?>;

            jQuery(document).ready(function($){


            /* WH causes error on load...
            jQuery('.learn')
              .popup({
                inline: false,
                on:'click',
                lastResort: 'bottom right',
            }); */

              jQuery('.show-more-tags').on("click",function(e){
                jQuery('.more-tags').show();
                jQuery(this).hide();
              });

            /* sticky? sidebar */   
            var $sidebar   = jQuery("#zbs-list-sidebar-wrap"), 
                $window    = jQuery(window),
                offset     = $sidebar.offset(),
                topPadding = 38;

                //does not work great on mobile / narrow
                if(window.outerWidth > 992){
                    $window.scroll(function() {
                        if(window.outerWidth > 992){
                            if ($window.scrollTop() > offset.top) {
                                $sidebar.stop().animate({
                                    marginTop: $window.scrollTop() - offset.top + topPadding
                                });
                            } else {
                                $sidebar.stop().animate({
                                    marginTop: 0
                                });
                            }
                        } else {
                            // reset
                            jQuery("#zbs-list-sidebar-wrap").css('margin-top','0');
                        }
                    });
                } else {
                    // reset
                    jQuery("#zbs-list-sidebar-wrap").css('margin-top','0');
                }


            });

            // General options for listview
            var zbsListViewSettings = {

                objdbname: '<?php echo $this->objType; ?>',
                search: true,
                filters: true,
                tags: true,
                c2c: true,
                editinline: <?php if (zeroBSCRM_getSetting('allowinlineedits') == "1") echo 'true'; else echo 'false'; ?>

            };

            // Vars for zbs list view drawer
            var zbsListViewParams = {
                listtype: '<?php echo $this->objType; ?>',
                columns: [
                    // e.g. {namestr:'Status',fieldstr:'_status'}
                    <?php  $colCount = 0; $inlineEditStr = '';

                        #} Current cols
                        if (is_array($currentColumns)) foreach ($currentColumns as $colKey => $col){

                            if ($colCount > 0) echo ',';

                            // can col be inline edited?
                            // WH made lazy solution here. (another arr)
                            $inline = '-1'; if (isset($allColumns[$colKey]) && isset($allColumns[$colKey]['editinline']) && $allColumns[$colKey]['editinline']) $inline = '1';
                            if (!empty($inlineEditStr)) $inlineEditStr .= ','; $inlineEditStr .= '"'.$colKey.'":'.$inline;
                            
                            #} Hard coded, lazy
                            echo "{namestr:'". zeroBSCRM_slashOut(__($col[0],"zero-bs-crm"),true) ."',fieldstr:'".zeroBSCRM_slashOut($colKey,true)."',inline:".$inline."}";

                            $colCount++;

                        }

                    ?>
                ],

                <?php if (zeroBSCRM_getSetting('allowinlineedits') == "1"){  ?>
                    editinline:{<?php echo $inlineEditStr; ?>},
                <?php }else{ ?>
                    editinline:'',
                <?php } ?>
                retrieved: false,
                count:<?php

                    #} Check for screen options (perpage)
                    $per_page = 20;
                    $screenOpts = $zbs->userScreenOptions();
                    if (is_array($screenOpts)){

                        if (isset($screenOpts['perpage'])) $per_page = (int)$screenOpts['perpage'];
                        // catch
                        if ($per_page < 1) $per_page = 20;

                    }

                    echo $per_page;
                    
                ?>,
                pagination: true,
                paged:<?php echo $currentPage; ?>,
                filters:<?php echo json_encode($listViewFilters); #} :) ?>,
                sort: <?php if (!empty($sort)) echo "'".$sort."'"; else echo 'false'; ?>,
                sortorder: <?php if (!empty($sortOrder)) echo "'".$sortOrder."'"; else echo 'false'; ?>,


                // expose page key (used to retrieve data with screen opts - perpage)
                pagekey: '<?php if (isset($zbs->pageKey)) echo $zbs->pageKey; ?>',

            };
            var zbsFilterButtons = [
                    // e.g. {namestr:'Status',fieldstr:'_status'}
                    <?php  $buttonCount = 0;

                        #} Current cols
                        if (is_array($currentFilterButtons)) foreach ($currentFilterButtons as $buttonKey => $button){

                            if ($buttonCount > 0) echo ',';
                            
                            #} Hard coded, lazy
                            echo "{namestr:'".zeroBSCRM_slashOut($button[0],true)."',fieldstr:'".$buttonKey."'}";

                            $buttonCount++;

                        }

                    ?>
                ];
            var zbsUnsortables = [<?php $c = 0; if (count($this->unsortables) > 0) foreach ($this->unsortables as $sortableStr) {

                        if ($c > 0) echo ',';

                        echo "'".zeroBSCRM_slashOut($sortableStr,true)."'";

                        $c++;
                        
            } ?>]; // this is columns that are "unsortable" e.g. edit link
            var zbsSortables = [<?php 

                $c = 0; if (count($this->sortables) > 0) foreach ($this->sortables as $sortableStr) {

                            if ($c > 0) echo ',';

                            echo "'".$sortableStr."'";

                            $c++;
                            
                } 

            ?>]; // for v2.2 this is only lot that will show sort, until we redo db this'll be hard
            var zbsBulkActions = [<?php $bulkCount = 0; if (count($this->bulkActions) > 0) foreach ($this->bulkActions as $bulkActionStr) {

                        if ($bulkCount > 0) echo ',';

                        echo "'".$bulkActionStr."'";

                        $bulkCount++;

            } ?>]; // :D
            var zbsListViewData = []; var zbsListViewCount = 0;
            var zbsDrawListViewBlocker = false;
            var zbsDrawListViewAJAXBlocker = false;
            var zbsDrawListViewColUpdateBlocker = false;
            var zbsDrawListViewColUpdateAJAXBlocker = false;
            var zbsDrawListLoadingBoxHTML = '<?php echo zeroBSCRM_UI2_loadingSegmentIncTextHTML(); ?>';
            var zbsObjectEditLinkPrefix = '<?php echo admin_url('post.php?action=edit&post='); ?>';
            var zbsObjectViewLinkPrefix = '<?php 

                // mike started rolling out a "view" (as well as edit),but only applies to customers for now
                if ($this->postType == 'zerobs_customer') 
                    echo zbsLink('view',-1,'zerobs_customer',true);
                else
                    echo admin_url('post.php?action=edit&post='); 

            ?>';
            var zbsObjectEmailLinkPrefix = '<?php 

                // this assumes is contact for now, just sends to prefill - perhaps later add mailto: optional (wh wants lol)
                echo zbsLink('email',-1,'zerobs_customer',true);

            ?>';
            var zbsObjectViewLinkPrefixCustomer = '<?php echo zbsLink('view',-1,'zerobs_customer',true); ?>';
            var zbsObjectViewLinkPrefixCompany = '<?php echo zbsLink('view',-1,'zerobs_company',true); ?>';
            var zbsObjectViewLinkPrefixQuote = '<?php echo zbsLink('edit',-1,'zerobs_quote',true); ?>';
            var zbsObjectViewLinkPrefixInvoice = '<?php echo zbsLink('edit',-1,'zerobs_invoice',true); ?>';
            var zbsObjectViewLinkPrefixTransaction = '<?php echo zbsLink('edit',-1,'zerobs_transaction',true); ?>';
            var zbsObjectViewLinkPrefixForm = '<?php echo zbsLink('edit',-1,ZBS_TYPE_FORM,true); ?>';
            var zbsObjectViewLinkPrefixSegment = '<?php echo zbsLink('edit',-1,ZBS_TYPE_SEGMENT,true); ?>';

            var zbsObjectEditLinkPrefixCustomer = '<?php echo zbsLink('edit',-1,'zerobs_customer',true); ?>';
            var zbsObjectEditLinkPrefixCompany = '<?php echo zbsLink('edit',-1,'zerobs_company',true); ?>';
            var zbsObjectEditLinkPrefixQuote = '<?php echo zbsLink('edit',-1,'zerobs_quote',true); ?>';
            var zbsObjectEditLinkPrefixQuoteTemplate = '<?php echo zbsLink('edit',-1,'zerobs_quo_template',true); ?>';
            var zbsObjectEditLinkPrefixInvoice = '<?php echo zbsLink('edit',-1,'zerobs_invoice',true); ?>';
            var zbsObjectEditLinkPrefixTransaction = '<?php echo zbsLink('edit',-1,'zerobs_transaction',true); ?>';
            var zbsObjectEditLinkPrefixForm = '<?php echo zbsLink('edit',-1,ZBS_TYPE_FORM,true); ?>';
            var zbsObjectEditLinkPrefixSegment = '<?php echo zbsLink('edit',-1,ZBS_TYPE_SEGMENT,true); ?>';
            
            var zbsListViewLink = '<?php echo admin_url('admin.php?page='.$this->postPage); ?>';
            var zbsExportPostURL = '<?php echo zeroBSCRM_getAdminURL($zbs->slugs['zbs-export-tools']); ?>';
            var zbsListViewTagFilterAffix = '&zbs_tag=';
            var zbsListViewQuickFilterAffix = '&quickfilters=';
            var zbsTagSkipLinkPrefix = zbsListViewLink + zbsListViewTagFilterAffix;
            var zbsListViewSearchFilterAffix = '&s=';
            var zbsListViewObjName = '<?php

                switch ($this->postType){


                    case 'zerobs_customer':
                        zeroBSCRM_slashOut(__('Contact',"zero-bs-crm"));
                        break;

                    case 'zerobs_company':
                        zeroBSCRM_slashOut(__('Company',"zero-bs-crm"));
                        break;

                    case 'zerobs_quote':
                        zeroBSCRM_slashOut(__('Quote',"zero-bs-crm"));
                        break;

                    case 'zerobs_invoice':
                        zeroBSCRM_slashOut(__('Invoice',"zero-bs-crm"));
                        break;

                    case 'zerobs_transaction':
                        zeroBSCRM_slashOut(__('Transaction',"zero-bs-crm"));
                        break;

                    case 'zerobs_form':
                        zeroBSCRM_slashOut(__('Form',"zero-bs-crm"));
                        break;

                    case 'zerobs_quotetemplate':
                        zeroBSCRM_slashOut(__('Quote Template',"zero-bs-crm"));
                        break;

                    default:
                        zeroBSCRM_slashOut(__('Item',"zero-bs-crm"));
                        break;



                } 

            ?>';
            var zbsListViewObjNamePlural = '<?php

                switch ($this->postType){


                    case 'zerobs_customer':
                        zeroBSCRM_slashOut(__('Contacts',"zero-bs-crm"));
                        break;

                    case 'zerobs_company':
                        zeroBSCRM_slashOut(__('Companies',"zero-bs-crm"));
                        break;

                    case 'zerobs_quote':
                        zeroBSCRM_slashOut(__('Quotes',"zero-bs-crm"));
                        break;

                    case 'zerobs_invoice':
                        zeroBSCRM_slashOut(__('Invoices',"zero-bs-crm"));
                        break;

                    case 'zerobs_transaction':
                        zeroBSCRM_slashOut(__('Transactions',"zero-bs-crm"));
                        break;

                    case 'zerobs_form':
                        zeroBSCRM_slashOut(__('Forms',"zero-bs-crm"));
                        break;

                    case 'zerobs_quotetemplate':
                        zeroBSCRM_slashOut(__('Quote Templates',"zero-bs-crm"));
                        break;

                    default:
                        zeroBSCRM_slashOut(__('Items',"zero-bs-crm"));
                        break;



                } 

            ?>';
            var zbsClick2CallType = parseInt('<?php echo zeroBSCRM_getSetting('clicktocalltype'); ?>');
            var zbsListViewLangLabels = {

                    'zbs_edit': '<?php zeroBSCRM_slashOut(__('Edit',"zero-bs-crm")); ?>',
                    'today': '<?php zeroBSCRM_slashOut(__('Today',"zero-bs-crm")); ?>',
                    'days': '<?php zeroBSCRM_slashOut(__('days',"zero-bs-crm")); ?>',
                    'daysago': '<?php zeroBSCRM_slashOut(__('days ago',"zero-bs-crm")); ?>',
                    'notcontacted': '<?php zeroBSCRM_slashOut(__('Not Contacted',"zero-bs-crm")); ?>',
                    'yesterday': '<?php zeroBSCRM_slashOut(__('Yesterday',"zero-bs-crm")); ?>',

                    // filtered by str
                    'filteredby': '<?php zeroBSCRM_slashOut(__('Filtered By',"zero-bs-crm")); ?>',
                    'notcontactedin': '<?php zeroBSCRM_slashOut(__('Not Contacted in',"zero-bs-crm")); ?>',
                    'containing': '<?php zeroBSCRM_slashOut(__('Containing',"zero-bs-crm")); ?>',
                    
                    // for inline-edits
                    'couldntupdate': '<?php zeroBSCRM_slashOut(__('Could not update',"zero-bs-crm")); ?>',
                    'couldntupdatedeets': '<?php zeroBSCRM_slashOut(__('This record could not be updated. Please try again, if this persists please let admin know.',"zero-bs-crm")); ?>',

                    <?php $labelCount = 0; 
                    if (count($this->langLabels) > 0) foreach ($this->langLabels as $labelK => $labelV){

                        if ($labelCount > 0) echo ',';

                        echo "'".$labelK."':'".zeroBSCRM_slashOut($labelV,true)."'";

                        $labelCount++;

                    } ?>

            };
            var zbsTagsForBulkActions = <?php 


                        // DAL 2 (contacts) or DAL 3 (All) switch:
                        if (($zbs->isDAL2() && $this->tag == 'zerobscrm_customertag') || $zbs->isDAL3()){

                            // make simplified
                            $simpleTerms = array(); if (is_array($terms) && count($terms) > 0) foreach ($terms as $t) $simpleTerms[] = array('id'=>$t['id'],'name'=>$t['name'],'slug'=>$t['slug']);

                        } else {

                            // make simplified
                            $simpleTerms = array(); if (is_array($terms) && count($terms) > 0) foreach ($terms as $t) $simpleTerms[] = array('id'=>$t->term_id,'name'=>$t->name,'slug'=>$t->slug);

                        }
                    echo json_encode($simpleTerms); 
            ?>;
            var zbsListViewIcos = {

                    // bulk action label icos
                    /* has to be unicode
                    deletetransactions: '<i class="fa fa-trash" aria-hidden="true"></i>',
                    addtags: '<i class="fa fa-tags" aria-hidden="true"></i>',
                    removetags: '<i class="fa fa-chain-broken" aria-hidden="true"></i>',
                    merge: '<i class="fa fa-compress" aria-hidden="true"></i>',
                    */

                    // ICONS playing up on semantic Select, so cut out for init.
                    /* 
                    deletetransactions: '&#xf1f8;',
                    addtags: '&#xf1f8;',
                    removetags: '&#xf1f8;',
                    merge: '&#xf1f8;',
                    */
                    
                    

            };
            // gives data used by inline editor
            var zbsListViewInlineEdit = {

                    // for now just put contacts in here
                    customer: {
                        statuses: <?php

                            // MUST be a better way than this to get customer statuses...
                            global $zbsCustomerFields;
                            if (is_array($zbsCustomerFields['status'][3])){

                                echo json_encode($zbsCustomerFields['status'][3]);

                            } else echo '[]';
                        ?>
                    },


                    owners: <?php

                        // hardcoded customer perms atm
                        $zbsPossibleOwners = zeroBS_getPossibleOwners(array('zerobs_admin','zerobs_customermgr'),true);
                        if (!is_array($zbsPossibleOwners))
                            echo json_encode(array());
                        else
                            echo json_encode($zbsPossibleOwners);

                    ?>
                    

            };
            <?php #} Nonce for AJAX
                echo 'var zbscrmjs_secToken = \''.wp_create_nonce( "zbscrmjs-ajax-nonce" ).'\';'; 

                // any last JS?
                if (isset($this->extraJS) && !empty($this->extraJS)) echo $this->extraJS;
            
            ?></script>

        </div><!-- // .wrap -->
        <?php

    } // /draw func

} // class