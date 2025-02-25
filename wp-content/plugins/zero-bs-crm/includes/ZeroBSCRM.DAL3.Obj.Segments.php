<?php 
/*!
 * Jetpack CRM
 * https://jetpackcrm.com
 * V3.0+
 *
 * Copyright 2020 Aut O’Mattic
 *
 * Date: 14/01/19
 */

/* ======================================================
  Breaking Checks ( stops direct access )
   ====================================================== */
    if ( ! defined( 'ZEROBSCRM_PATH' ) ) exit;
/* ======================================================
  / Breaking Checks
   ====================================================== */



/**
* ZBS DAL >> Segments
*
* @author   Woody Hayday <hello@jetpackcrm.com>
* @version  2.0
* @access   public
* @see      https://jetpackcrm.com/kb
*/
class zbsDAL_segments extends zbsDAL_ObjectLayer {

    protected $objectType = ZBS_TYPE_SEGMENT;
    protected $objectModel = array(

        // ID
        'ID' => array('fieldname' => 'ID', 'format' => 'int'),

        // site + team generics
        'zbs_site' => array('fieldname' => 'zbs_site', 'format' => 'int'),
        'zbs_team' => array('fieldname' => 'zbs_team', 'format' => 'int'),
        'zbs_owner' => array('fieldname' => 'zbs_owner', 'format' => 'int'),

        // other fields
        'name' => array('fieldname' => 'zbsseg_name', 'format' => 'str'),
        'slug' => array('fieldname' => 'zbsseg_slug', 'format' => 'str'),
        'matchtype' => array('fieldname' => 'zbsseg_matchtype', 'format' => 'str'),
        'created' => array('fieldname' => 'zbsseg_created', 'format' => 'uts'),
        'lastupdated' => array('fieldname' => 'zbsseg_lastupdated', 'format' => 'uts'),
        'compilecount' => array('fieldname' => 'zbsseg_compilecount', 'format' => 'int'),
        'lastcompiled' => array('fieldname' => 'zbsseg_lastcompiled', 'format' => 'uts'),

        );


    function __construct($args=array()) {


        #} =========== LOAD ARGS ==============
        $defaultArgs = array(

            //'tag' => false,

        ); foreach ($defaultArgs as $argK => $argV){ $this->$argK = $argV; if (is_array($args) && isset($args[$argK])) {  if (is_array($args[$argK])){ $newData = $this->$argK; if (!is_array($newData)) $newData = array(); foreach ($args[$argK] as $subK => $subV){ $newData[$subK] = $subV; }$this->$argK = $newData;} else { $this->$argK = $args[$argK]; } } }
        #} =========== / LOAD ARGS =============


    }


    // ===============================================================================
    // ===========   SEGMENTS  =======================================================

    // generic get Company (by ID)
    // Super simplistic wrapper used by edit page etc. (generically called via dal->contacts->getSingle etc.)
    public function getSingle($ID=-1){

        return $this->getSegment($ID);

    }
    
    // This was actually written pre DAL2 and so still has some legacy layout of func 
    // etc. To be slowly refined if needed.
    
           
   /**
     * get a segment (header line)
     */
    public function getSegment($segmentID=-1,$withConditions=false,$checkOwnershipID=false){

        if ($segmentID > 0){
    
            global $ZBSCRM_t,$wpdb;

            $additionalWHERE = ''; $queryVars = array($segmentID);

            // check ownership
            // THIS ShoULD BE STANDARDISED THROUGHOUT DAL (ON DB2)
                // $checkOwnershipID = ID = check against that ID
                // $checkOwnershipID = true = check against get_current_user_id
                // $checkOwnershipID = false = do not check
            
            if ($checkOwnershipID === true){

                $segmentOwner = get_current_user_id();

            } else if ($checkOwnershipID > 0){

                $segmentOwner = (int)$checkOwnershipID;

            } // else is false, don't test

            if (isset($segmentOwner)){

                // add check
                $additionalWHERE = 'AND zbs_owner = %d';
                $queryVars[] = $segmentOwner;

            }
            

            $potentialSegment = $wpdb->get_row( $this->prepare("SELECT * FROM ".$ZBSCRM_t['segments']." WHERE ID = %d ".$additionalWHERE."ORDER BY ID ASC LIMIT 0,1",$queryVars), OBJECT );

            if (isset($potentialSegment) && isset($potentialSegment->ID)){

                #} Retrieved :) fill + return
                
                    // tidy
                    $segment = $this->tidy_segment($potentialSegment);

                    if ($withConditions) {

                        $segment['conditions'] = $this->getSegmentConditions($segment['id']);

                    }


                return $segment;
            }

        }

        return false;
    
   }

     /**
     * get Sements Pass -1 for $perPage and $page and this'll return ALL
     */
    public function getSegments($ownerID=-1,$perPage=10,$page=0,$withConditions=false,$searchPhrase='',$inArr='',$sortByField='',$sortOrder='DESC'){

                global $zbs,$ZBSCRM_t,$wpdb;

                $segments = false;

                // build query
                $sql = "SELECT * FROM ".$ZBSCRM_t['segments'];
                $wheres = array();
                $params = array();
                $orderByStr = '';

                    // Owner

                        // escape (all)
                        if ($ownerID != -99){

                            if ($ownerID === -1) $ownerID = get_current_user_id();

                            if (!empty($ownerID)) $wheres['zbs_owner'] = array('=',$ownerID,'%d');

                        }


                    // search phrase
                    if (!empty($searchPhrase)){

                        $wheres['zbsseg_name'] = array('LIKE','%'.$searchPhrase.'%','%s');

                    }

                    // in array
                    if (is_array($inArr) && count($inArr) > 0){

                        $wheres['ID'] = array('IN','('.implode(',', $inArr).')','%s');

                    }

                    // add where's to SQL
                    // + 
                    // feed in params
                    $whereStr = '';
                    if (count($wheres) > 0) foreach ($wheres as $key => $whereArr) {

                        if (!empty($whereStr)) 
                            $whereStr .= ' AND ';
                        else
                            $whereStr .= ' WHERE ';

                        // add in - NOTE: this is TRUSTING key + whereArr[0]
                        $whereStr .= $key.' '.$whereArr[0].' '.$whereArr[2];

                        // feed in params
                        $params[] = $whereArr[1];
                    }

                    // append to sql
                    $sql .= $whereStr;



                    // sort by
                    if (!empty($sortByField)){

                        if (!in_array($sortOrder, array('DESC','ASC'))) $sortOrder = 'DESC';

                        // parametise order field as is unchecked
                        //$orderByStr = ' ORDER BY %s '.$sortOrder;
                        //$params[] = $sortByField;
                        $orderByStr = ' ORDER BY '.$sortByField.' '.$sortOrder;

                    }


                    // pagination
                    if ($page == -1 && $perPage == -1){

                        // NO LIMITS :o


                    } else {

                        // Because SQL USING zero indexed page numbers, we remove -1 here
                        // ... DO NOT change this without seeing usage of the function (e.g. list view) - which'll break
                        $page = (int)$page-1;
                        if ($page < 0) $page = 0;

                        // check params realistic
                        // todo, for now, brute pass
                        $orderByStr .= ' LIMIT '.(int)$page.','.(int)$perPage;

                    }

                    // append to sql
                    $sql .= $orderByStr;

                $query = $this->prepare($sql,$params);

                try {

                    $potentialSegments = $wpdb->get_results( $query, OBJECT );

                } catch (Exception $e){

                    // error with sql :/ for now nothin

                }

                if (isset($potentialSegments) && is_array($potentialSegments)) $segments = $potentialSegments;

                // TIDY
                $res = array();
                if (count($segments) > 0) foreach ($segments as $segment) {
                                
                                // tidy
                                $resArr = $this->tidy_segment($segment);

                                // TO ADD to query / here withConditions
                                // TODO: REFACTOR into query? More efficient?
                                if ($withConditions) $resArr['conditions'] = $this->getSegmentConditions($segment->ID);

                                $res[] = $resArr;

                            }

                return $res;
            
           }

           // brutal simple temp func (should be a wrapper really. segments to tidy up post DAL2 other obj)
           public function getSegmentCount(){

                global $ZBSCRM_t,$wpdb;

                // build query
                $sql = "SELECT COUNT(ID) FROM ".$ZBSCRM_t['segments'];

                return $wpdb->get_var($sql);
            
           }


             /**
             * deletes a Segment object (and its conditions)
             *
             * @param array $args Associative array of arguments
             *              id
             *
             * @return int success;
             */
            public function deleteSegment($args=array()){

                global $ZBSCRM_t, $wpdb, $zbs;

                #} ============ LOAD ARGS =============
                $defaultArgs = array(

                    'id'            => -1,
                    'saveOrphans'   => -1

                ); foreach ($defaultArgs as $argK => $argV){ $$argK = $argV; if (is_array($args) && isset($args[$argK])) {  if (is_array($args[$argK])){ $newData = $$argK; if (!is_array($newData)) $newData = array(); foreach ($args[$argK] as $subK => $subV){ $newData[$subK] = $subV; }$$argK = $newData;} else { $$argK = $args[$argK]; } } }
                #} =========== / LOAD ARGS ============

                #} Check ID & Delete :)
                $id = (int)$id;
                if (!empty($id) && $id > 0) {

                    $segment = $this->getSegment( $id );

                    $deleted = zeroBSCRM_db2_deleteGeneric($id,'segments');

                        // delete segment conditions?
                        // check $deleted?

                        $del = $wpdb->delete( 
                                    $ZBSCRM_t['segmentsconditions'], 
                                    array( // where
                                        'zbscondition_segmentid' => $id
                                        ),
                                    array(
                                        '%d'
                                        )
                                    );

                        #} Add to automator
                        zeroBSCRM_FireInternalAutomator('segment.delete',array(
                            'id'            => $id,
                            'saveOrphans'   => $saveOrphans,
                        ));

                        $customViews = $zbs->settings->get('customviews2');
                        $segment_slug = $segment['slug'];
                        unset( $customViews['customer_filters']["segment_$segment_slug"] );
                        $zbs->settings->update('customviews2', $customViews);

                        return $del;

                }

                return false;

            }

             /**
             * tidys a segment
             */
            public function tidy_segment($obj=false){

                $res = false;


                if (isset($obj->ID)){
                    $res = array();
                    $res['id'] = $obj->ID;
                    
                    $res['name'] = $obj->zbsseg_name;
                    $res['slug'] = $obj->zbsseg_slug;
                    $res['matchtype'] = $obj->zbsseg_matchtype;

                    $res['created'] = $obj->zbsseg_created;
                    $res['lastupdated'] = $obj->zbsseg_lastupdated;
                    $res['compilecount'] = $obj->zbsseg_compilecount;
                    $res['lastcompiled'] = $obj->zbsseg_lastcompiled;

                    // pretty date outputs for list viw
                    $res['createddate'] = zeroBSCRM_locale_utsToDate($obj->zbsseg_created);
                    $res['lastcompileddate'] = zeroBSCRM_locale_utsToDate($obj->zbsseg_lastcompiled);
                } 

                return $res;

           }

             /**
             * tidys a segment condition
             */
            public function tidy_segment_condition($obj=false){

                $res = false;

                if (isset($obj->ID)){
                    $res = array();
                    $res['id'] = $obj->ID;
                    
                    $res['segmentID'] = $obj->zbscondition_segmentid;
                    $res['type'] = $obj->zbscondition_type;
                    $res['operator'] = $obj->zbscondition_op;
                    $res['value'] = zeroBSCRM_textExpose($obj->zbscondition_val);
                    $res['value2'] = zeroBSCRM_textExpose($obj->zbscondition_val_secondary);

                    // applies any necessary conversions e.g. uts -> date
                    $res['valueconv'] = zeroBSCRM_segments_typeConversions($res['value'],$res['type'],$res['operator'],'out');
                    $res['value2conv'] = zeroBSCRM_segments_typeConversions($res['value2'],$res['type'],$res['operator'],'out');

                } 

                return $res;

           }

           
           /**
             * This is designed to mimic zeroBS_getSegments, but only to return a total count :) 
             */
            public function getSegmentsCountIncParams($ownerID=-1,$perPage=10,$page=0,$withConditions=false,$searchPhrase='',$inArr='',$sortByField='',$sortOrder='DESC'){

                global $zbs,$ZBSCRM_t,$wpdb;

                $segmentCount = false;

                // build query
                $sql = "SELECT COUNT(ID) segcount FROM ".$ZBSCRM_t['segments'];
                $wheres = array();
                $params = array();
                $orderByStr = '';

                    // Owner

                        // escape (all)
                        if ($ownerID != -99){

                            if ($ownerID === -1) $ownerID = get_current_user_id();

                            if (!empty($ownerID)) $wheres['zbs_owner'] = array('=',$ownerID,'%d');

                        }


                    // search phrase
                    if (!empty($searchPhrase)){

                        $wheres['zbsseg_name'] = array('LIKE',$searchPhrase,'%s');

                    }

                    // in array
                    if (is_array($inArr) && count($inArr) > 0){

                        $wheres['ID'] = array('IN','('.implode(',', $inArr).')','%s');

                    }

                    // add where's to SQL
                    // + 
                    // feed in params
                    $whereStr = '';
                    if (count($wheres) > 0) foreach ($wheres as $key => $whereArr) {

                        if (!empty($whereStr)) 
                            $whereStr .= ' AND ';
                        else
                            $whereStr .= ' WHERE ';

                        // add in - NOTE: this is TRUSTING key + whereArr[0]
                        $whereStr .= $key.' '.$whereArr[0].' '.$whereArr[2];

                        // feed in params
                        $params[] = $whereArr[1];
                    }

                    // append to sql
                    $sql .= $whereStr;

                $query = $this->prepare($sql,$params);

                try {

                    $potentialSegmentCount = $wpdb->get_row( $query, OBJECT );

                } catch (Exception $e){

                    // error with sql :/ for now nothin

                }

                if (isset($potentialSegmentCount) && isset($potentialSegmentCount->segcount)) $segmentCount = $potentialSegmentCount->segcount;

                return $segmentCount;
            
           }


           /**
             * builds a preview (top 5 + count) of a set of conditions which could be against a segment
             * expects a filtered list of conditions (e.g. zeroBSCRM_segments_filterConditions if sent through POST)
             */
            public function previewSegment($conditions=array(),$matchType='all',$countOnly=false){

                    // retrieve getContacts arguments from a list of segment conditions
                    $contactGetArgs = $this->segmentConditionsToArgs($conditions,$matchType);

                    // add top 5 + count params
                    $contactGetArgs['sortByField'] = 'ID';
                    $contactGetArgs['sortOrder'] = 'DESC';
                    $contactGetArgs['page'] = 0;
                    $contactGetArgs['perPage'] = 5;
                    $contactGetArgs['ignoreowner'] = zeroBSCRM_DAL2_ignoreOwnership(ZBS_TYPE_CONTACT);

                    // count ver
                    $countContactGetArgs = $contactGetArgs;
                    $countContactGetArgs['perPage'] = 100000;
                    $countContactGetArgs['count'] = true;

                    // count only
                    if ($countOnly) return $this->DAL()->contacts->getContacts($countContactGetArgs);

                    // Retrieve
                    return array(
                        // DEBUG 
                        //'conditions' => $conditions, // TEMP - remove this
                        //'args' => $contactGetArgs, // TEMP - remove this
                        'count'=>$this->DAL()->contacts->getContacts($countContactGetArgs),
                        'list'=>$this->DAL()->contacts->getContacts($contactGetArgs)
                    );

           }


           /**
             * used by previewSegment and getSegmentAudience to build condition args
             */
           public function segmentConditionsToArgs($conditions=array(),$matchType='all'){

                    if (is_array($conditions) && count($conditions) > 0){

                            $contactGetArgs = array();
                            $conditionIndx = 0; // this allows multiple queries for SAME field (e.g. status = x or status = y)

                            // cycle through & add to contact request arr
                            foreach ($conditions as $condition){

                                $newArgs = $this->segmentConditionArgs($condition,$conditionIndx); $additionalWHERE = false;

                                // legit? merge (must be recursive)
                                if (is_array($newArgs)) $contactGetArgs = array_merge_recursive($contactGetArgs,$newArgs);

                                $conditionIndx++;

                            }

                            // match type ALL is default, this switches to ANY
                            if ($matchType == 'one') $contactGetArgs['whereCase'] = 'OR';

                            return $contactGetArgs;
                        }

                    return array();

           }
           
           /**
             * get a segment (header line)
             */
            public function getSegmentBySlug($segmentSlug=-1,$withConditions=false,$checkOwnershipID=false){

                if (!empty($segmentSlug)){
            
                    global $ZBSCRM_t,$wpdb;

                    $additionalWHERE = ''; $queryVars = array($segmentSlug);

                    // check ownership
                    // THIS ShoULD BE STANDARDISED THROUGHOUT DAL (ON DB2)
                        // $checkOwnershipID = ID = check against that ID
                        // $checkOwnershipID = true = check against get_current_user_id
                        // $checkOwnershipID = false = do not check
                    
                    if ($checkOwnershipID === true){

                        $segmentOwner = get_current_user_id();

                    } else if ($checkOwnershipID > 0){

                        $segmentOwner = (int)$checkOwnershipID;

                    } // else is false, don't test

                    if (isset($segmentOwner)){

                        // add check
                        $additionalWHERE = 'AND zbs_owner = %d';
                        $queryVars[] = $segmentOwner;

                    }
                    

                    $potentialSegment = $wpdb->get_row( $this->prepare("SELECT * FROM ".$ZBSCRM_t['segments']." WHERE zbsseg_slug = %s ".$additionalWHERE."ORDER BY ID ASC LIMIT 0,1",$queryVars), OBJECT );

                    if (isset($potentialSegment) && isset($potentialSegment->ID)){

                        #} Retrieved :) fill + return
                        
                            // tidy
                            $segment = $this->tidy_segment($potentialSegment);

                            if ($withConditions) {

                                $segment['conditions'] = $this->getSegmentConditions($segment['id']);

                            }


                        return $segment;
                    }

                }

                return false;

            }

           /**
             * Runs a filtered search on customers based on a segment's condition
             * returns array or count ($onlyCount)
             */
            public function getSegementAudience($segmentID=-1,$page=0,$perPage=20,$sortByField='ID',$sortOrder='DESC',$onlyCount=false,$withDND=false){

                // assumes sensible paging + sort vars... no checking of them

                if ($segmentID > 0){

                    #} Retrieve segment + conditions
                    $segment = $this->getSegment($segmentID,true);
                    $conditions = array(); if (isset($segment['conditions'])) $conditions = $segment['conditions'];
                    $matchType = 'all'; if (isset($segment['matchtype'])) $matchType = $segment['matchtype'];

                    // retrieve getContacts arguments from a list of segment conditions
                    $contactGetArgs = $this->segmentConditionsToArgs($conditions,$matchType);

                        // needs to be ownerless for now
                        $contactGetArgs['ignoreowner'] = zeroBSCRM_DAL2_ignoreOwnership(ZBS_TYPE_CONTACT);

                        // add paging params
                        $contactGetArgs['sortByField'] = $sortByField;
                        $contactGetArgs['sortOrder'] = $sortOrder;
                        $contactGetArgs['page'] = $page;
                        if ($perPage !== -1)
                            $contactGetArgs['perPage'] = $perPage; // over 100k? :o
                        else { 
                            // no limits
                            $contactGetArgs['page'] = -1;
                            $contactGetArgs['perPage'] = -1;
                        }

                        // count ver
                        if ($onlyCount){
                            $contactGetArgs = $contactGetArgs;
                            $contactGetArgs['page'] = -1;
                            $contactGetArgs['perPage'] = -1;
                            $contactGetArgs['count'] = true;

                            $count = $this->DAL()->contacts->getContacts($contactGetArgs);

                                // effectively a compile, so update compiled no on record
                                $this->updateSegmentCompiled($segmentID,$count,time());

                            return $count;
                        }

                        // got dnd?
                        if ($withDND) $contactGetArgs['withDND'] = true;

                        $contacts = $this->DAL()->contacts->getContacts($contactGetArgs);

                        // if no limits, update compile record (effectively a compile)
                        if ($contactGetArgs['page'] == -1 && $contactGetArgs['perPage'] == -1){

                            $this->updateSegmentCompiled($segmentID,count($contacts),time());

                        }
           
                        // Retrieve
                        return $contacts;

                }

                return false;

           }

           /**
             * checks all segments against a contact
             */
            public function getSegmentsContainingContact($contactID=-1,$justIDs=false){

                $ret = array();

                if ($contactID > 0){

                    // get all segments
                    $segments = $this->getSegments(-1,1000,0,true);

                    if (count($segments) > 0) foreach ($segments as $segment){

                        // pass obj to check (saves it querying)
                        if ($this->isContactInSegment($contactID, $segment['id'],$segment)){

                            // is in segment
                            if ($justIDs)
                                $ret[] = $segment['id'];
                            else
                                $ret[] = $segment;

                        }

                    } // foreach segment

                } // if contact id

                return $ret;

           }

           /**
             * Checks if a contact matches segment conditions
             * ... can pass $segmentObj to avoid queries (performance) if already have it
             */
            public function isContactInSegment($contactID=-1,$segmentID=-1,$segmentObj=false){

                if ($segmentID > 0 && $contactID > 0){

                    #} Retrieve segment + conditions
                    if (is_array($segmentObj)) 
                        $segment = $segmentObj;
                    else
                        $segment = $this->getSegment($segmentID,true);

                    #} Set these
                    $conditions = array(); if (isset($segment['conditions'])) $conditions = $segment['conditions'];
                    $matchType = 'all'; if (isset($segment['matchtype'])) $matchType = $segment['matchtype'];

                    // retrieve getContacts arguments from a list of segment conditions
                    $contactGetArgs = $this->segmentConditionsToArgs($conditions,$matchType);

                        // add paging params
                        $contactGetArgs['page'] = -1;
                        $contactGetArgs['perPage'] = -1;
                        $contactGetArgs['count'] = true;

                        // add id check (via rough additionalWhere)
                        if (!isset($contactGetArgs['additionalWhereArr'])) $contactGetArgs['additionalWhereArr'] = array();
                        $contactGetArgs['additionalWhereArr']['idCheck'] = array("ID",'=','%d',$contactID);

                        // should only ever be 1 or 0
                        $count = $this->DAL()->contacts->getContacts($contactGetArgs);

                        if ($count == 1) 
                            return true;

                        // nope.
                        return false;

                }

                return false;

           }

           /**
             * Compiles any segments which are affected on a single contact change
             * includeSegments is an array of id's - this allows you to pass 'what contact was in before' (because these need --1)
             */
            public function compileSegmentsAffectedByContact($contactID=-1,$includeSegments=array()){

                if ($contactID > 0){

                    // get all segments
                    $segments = $this->getSegments(-1,1000,0,true);

                    if (count($segments) > 0) foreach ($segments as $segment){

                        // pass obj to check (saves it querying)
                        if ($this->isContactInSegment($contactID, $segment['id'],$segment) || in_array($segment['id'], $includeSegments)){

                            // is in segment

                            // compile this segment
                            $this->compileSegment($segment['id']);

                        }

                    } // foreach segment

                } // if contact id

                return false;

           }


           
           /**
             * 
             */
            public function getSegmentConditions($segmentID=-1){

                if ($segmentID > 0){

                    global $ZBSCRM_t,$wpdb;

                    $potentialSegmentConditions = $wpdb->get_results( $this->prepare("SELECT * FROM ".$ZBSCRM_t['segmentsconditions']." WHERE zbscondition_segmentid = %d",$segmentID) );

                    if (is_array($potentialSegmentConditions) && count($potentialSegmentConditions) > 0) {

                        $returnConditions = array();

                        foreach ($potentialSegmentConditions as $condition){

                            $returnConditions[] = $this->tidy_segment_condition($condition);

                        }


                        return $returnConditions;

                    }
                    

                }

                return false;
            
           }


           /**
             * Simple func to update the segment compiled count (says how many contacts currently in segment)
             */
           public function updateSegmentCompiled($segmentID=-1,$segmentCount=0,$compiledUTS=-1){
                
                global $ZBSCRM_t,$wpdb;

                if ($segmentID > 0){

                    // checks
                    $count = 0; if ($segmentCount > 0) $count = (int)$segmentCount;
                    $compiled = time(); if ($compiledUTS > 0) $compiled = (int)$compiledUTS;

                    if ($wpdb->update( 
                            $ZBSCRM_t['segments'], 
                            array( 
                                'zbsseg_compilecount' => $count,
                                'zbsseg_lastcompiled' => $compiled
                            ), 
                            array( // where
                                'ID' => $segmentID
                                ),
                            array( 
                                '%d', 
                                '%d'
                            ),
                            array(
                                '%d'
                                )
                            ) !== false){

                            // udpdated
                            return true;

                        } else {

                            // could not update?!
                            return false;

                        }


                }

           }

           /**
             * 
             */
            public function addUpdateSegment($segmentID=-1,$segmentOwner=-1,$segmentName='',$segmentConditions=array(),$segmentMatchType='all',$forceCompile=false){

                global $ZBSCRM_t,$wpdb;

                #} After ops, shall I compile audience?
                $toCompile = $forceCompile;

                if ($segmentID > 0){

                    #} Update a segment

                        #} Owner - if -1 then use current user
                        if ($segmentOwner <= 0) $segmentOwner = get_current_user_id();

                        #} Empty name = untitled
                        if (empty($segmentName)) $segmentName = __('Untitled Segment',"zero-bs-crm");

                        // slug auto-updates with name, (fix later if issue)
                        // in fact, just leave as whatever first set? (affects quickfilter URLs etc?)
                        // just did in end
                        #} Generate slug
                        $segmentSlug = $this->makeSlug($segmentName);

                        #} update header line
                        if ($wpdb->update( 
                            $ZBSCRM_t['segments'], 
                            array( 
                                'zbs_owner' => $segmentOwner,
                                'zbsseg_name' => $segmentName,
                                'zbsseg_slug' => $segmentSlug,
                                'zbsseg_matchtype' => $segmentMatchType,
                                'zbsseg_lastupdated' => time()
                            ), 
                            array( // where
                                'ID' => $segmentID
                                ),
                            array( 
                                '%d', 
                                '%s',
                                '%s',
                                '%s',
                                '%d'
                            ),
                            array(
                                '%d'
                                )
                            ) !== false){

                            // updated, move on..

                            // add segment conditions
                            $this->addUpdateSegmentConditions($segmentID,$segmentConditions);

                            // return id
                            $returnID = $segmentID;

                            // force to compile
                            $toCompile = true; $compileID = $segmentID;

                        } else {

                            // could not update?!
                            return false;

                        }
                    

                } else {

                    #} Add a new segment

                        #} Owner - if -1 then use current user
                        if ($segmentOwner <= 0) $segmentOwner = get_current_user_id();

                        #} Empty name = untitled (should never happen because of UI)
                        if (empty($segmentName)) $segmentName = __('Untitled Segment',"zero-bs-crm");

                        #} Generate slug
                        $segmentSlug = $this->makeSlug($segmentName);

                        #} Add header line
                        if ($wpdb->insert( 
                            $ZBSCRM_t['segments'], 
                            array( 
                                'zbs_owner' => $segmentOwner,
                                'zbsseg_name' => $segmentName,
                                'zbsseg_slug' => $segmentSlug,
                                'zbsseg_matchtype' => $segmentMatchType,
                                'zbsseg_created' => time(),
                                'zbsseg_lastupdated' => time(),
                                'zbsseg_lastcompiled' => time(), // we'll compile it shortly, set as now :)
                            ), 
                            array( 
                                '%d', 
                                '%s',
                                '%s',
                                '%s',
                                '%d',
                                '%d',
                                '%d'
                            ) 
                        ) > 0){

                            // inserted, let's move on
                            $newSegmentID = $wpdb->insert_id;

                            // add segment conditions
                            $this->addUpdateSegmentConditions($newSegmentID,$segmentConditions);

                            // force to compile
                            $toCompile = true; $compileID = $newSegmentID;

                            // return id
                            $returnID = $newSegmentID;

                        } else {

                            // could not insert?!
                            return false;

                        }

                } // / new

                // "compile" segments?
                if ($toCompile && !empty($compileID)){

                    // compiles + logs how many in segment against record
                    $totalInSegment = $this->compileSegment($compileID);

                }

                if (isset($returnID))
                    return $returnID;
                else
                    return false;
            
           }


           public function addUpdateSegmentConditions($segmentID=-1,$conditions=array()){

                if ($segmentID > 0 && is_array($conditions)){

                    // lazy - here I NUKE all existing conditions then readd...
                    $this->removeSegmentConditions($segmentID);

                        if (is_array($conditions) && count($conditions) > 0){

                            $retConditions = array();

                            foreach ($conditions as $sCondition){


                                $newConditionID = $this->addUpdateSegmentCondition(-1,$segmentID,$sCondition);

                                if (!empty($newConditionID)){

                                    // new condition added, insert
                                    $retConditions[$newConditionID] = $sCondition;

                                } else {

                                    // error inserting condition?!
                                    return false;

                                }

                            }

                            return $retConditions;

                        }


                } 

                return array();

           }

           /**
             * 
             */
            public function addUpdateSegmentCondition($conditionID=-1,$segmentID=-1,$conditionDetails=array()){

                global $ZBSCRM_t,$wpdb;

                #} Check/build empty condition details
                $condition = array(
                    'type' => '',
                    'operator' => '',
                    'val' => '',
                    'valsecondary' => ''
                );
                if (isset($conditionDetails['type'])) $condition['type'] = $conditionDetails['type'];
                if (isset($conditionDetails['value'])) $condition['val'] = $conditionDetails['value'];
                if (isset($conditionDetails['operator']) && $conditionDetails['operator'] !== -1) $condition['operator'] = $conditionDetails['operator'];
                if (isset($conditionDetails['value2'])) $condition['valsecondary'] = $conditionDetails['value2'];

                // update or insert?
                if ($conditionID > 0){

                    #} Update a segment condition

                        #} update line
                        if ($wpdb->update( 
                            $ZBSCRM_t['segmentsconditions'], 
                            array( 
                                'zbscondition_segmentid' => $segmentID,
                                'zbscondition_type' => $condition['type'],
                                'zbscondition_op' => $condition['operator'],
                                'zbscondition_val' => $condition['val'],
                                'zbscondition_val_secondary' => $condition['valsecondary']
                            ), 
                            array( // where
                                'ID' => $conditionID
                                ),
                            array( 
                                '%d', 
                                '%s',
                                '%s',
                                '%s',
                                '%s'
                            ),
                            array(
                                '%d'
                                )
                            ) !== false){

                            return $conditionID;

                        } else {

                            // could not update?!
                            return false;

                        }
                    

                } else {

                    #} Add a new segmentcondition


                        #} Add condition line
                        if ($wpdb->insert( 
                            $ZBSCRM_t['segmentsconditions'], 
                            array( 
                                'zbscondition_segmentid' => $segmentID,
                                'zbscondition_type' => $condition['type'],
                                'zbscondition_op' => $condition['operator'],
                                'zbscondition_val' => $condition['val'],
                                'zbscondition_val_secondary' => $condition['valsecondary']
                            ), 
                            array( 
                                '%d', 
                                '%s',
                                '%s',
                                '%s',
                                '%s'
                            ) 
                        ) > 0){


                            // inserted
                            return $wpdb->insert_id;

                        } else {

                            // could not insert?!
                            return false;

                        }

                } // / new

                return false;

            
           }

           /**
             *  empty all conditions against seg
             */
            public function removeSegmentConditions($segmentID=-1){

                if (!empty($segmentID)) {

                    global $ZBSCRM_t,$wpdb;

                    return $wpdb->delete( 
                                $ZBSCRM_t['segmentsconditions'], 
                                array( // where
                                    'zbscondition_segmentid' => $segmentID
                                    ),
                                array(
                                    '%d'
                                    )
                                );

                }

                return false;
            
           }



           /**
             * Segment rules
             *  takes a condition + returns a contact dal2 get arr param
             */
            public function segmentConditionArgs($condition=array(),$conditionKeySuffix=''){

                if (is_array($condition) && isset($condition['type']) && isset($condition['operator'])){

                    global $zbs,$wpdb,$ZBSCRM_t;

                    switch ($condition['type']){

                        case 'status':

                        /* while this is right, it doesn't allow for MULTIPLE status cond lines, so do via sql:
                            if ($condition['operator'] == 'equal')
                                return array('hasStatus'=>$condition['value']);
                            else
                                return array('otherStatus'=>$condition['value']);
                        */
                            if ($condition['operator'] == 'equal')
                                return array('additionalWhereArr'=>
                                            array('statusEqual'.$conditionKeySuffix=>array("zbsc_status",'=','%s',$condition['value']))
                                        );
                            else
                                return array('additionalWhereArr'=>
                                            array('statusEqual'.$conditionKeySuffix=>array("zbsc_status",'<>','%s',$condition['value']))
                                        );

                            break;

                        case 'fullname': // 'equal','notequal','contains'

                            if ($condition['operator'] == 'equal')
                                return array('additionalWhereArr'=>
                                            array('fullnameEqual'.$conditionKeySuffix=>array("CONCAT(zbsc_fname,' ',zbsc_lname)",'=','%s',$condition['value']))
                                        );
                            else if ($condition['operator'] == 'notequal')
                                return array('additionalWhereArr'=>
                                            array('fullnameEqual'.$conditionKeySuffix=>array("CONCAT(zbsc_fname,' ',zbsc_lname)",'<>','%s',$condition['value']))
                                        );
                            else if ($condition['operator'] == 'contains')
                                return array('additionalWhereArr'=>
                                            array('fullnameEqual'.$conditionKeySuffix=>array("CONCAT(zbsc_fname,' ',zbsc_lname)",'LIKE','%s','%'.$condition['value'].'%'))
                                        );
                            break;

                        case 'email': // 'equal','notequal','contains'

                            if ($condition['operator'] == 'equal'){
                                // while this is right, it doesn't allow for MULTIPLE status cond lines, so do via sql:
                                // return array('hasEmail'=>$condition['value']);
                                /* // this was good, but was effectively AND
                                return array('additionalWhereArr'=>
                                            array(
                                                'email'.$conditionKeySuffix=>array('zbsc_email','=','%s',$condition['value']),
                                                'emailAKA'.$conditionKeySuffix=>array('ID','IN',"(SELECT aka_id FROM ".$ZBSCRM_t['aka']." WHERE aka_type = ".ZBS_TYPE_CONTACT." AND aka_alias = %s)",$condition['value'])
                                                )
                                        );
                                */
                                // This was required to work with OR (e.g. postcode 1 = x or postcode 2 = x)
                                // -----------------------
                                // This generates a query like 'zbsc_fname LIKE %s OR zbsc_lname LIKE %s', 
                                // which we then need to include as direct subquery
                                /* THIS WORKS: but refactored below
                                $conditionQArr = $this->buildWheres(array(
                                                                    'email'.$conditionKeySuffix=>array('zbsc_email','=','%s',$condition['value']),
                                                                    'emailAKA'.$conditionKeySuffix=>array('ID','IN',"(SELECT aka_id FROM ".$ZBSCRM_t['aka']." WHERE aka_type = ".ZBS_TYPE_CONTACT." AND aka_alias = %s)",$condition['value'])
                                                                    ),'',array(),'OR',false);
                                if (is_array($conditionQArr) && isset($conditionQArr['where']) && !empty($conditionQArr['where'])){                                    
                                    return array('additionalWhereArr'=>array('direct'=>array(array('('.$conditionQArr['where'].')',$conditionQArr['params']))));
                                }
                                return array();
                                */
                                // this way for OR situations
                                return $this->segmentBuildDirectOrClause(array(
                                                                    'email'.$conditionKeySuffix=>array('zbsc_email','=','%s',$condition['value']),
                                                                    'emailAKA'.$conditionKeySuffix=>array('ID','IN',"(SELECT aka_id FROM ".$ZBSCRM_t['aka']." WHERE aka_type = ".ZBS_TYPE_CONTACT." AND aka_alias = %s)",$condition['value'])
                                                                    ),'OR');
                                // -----------------------
                            } else if ($condition['operator'] == 'notequal')
                                return array('additionalWhereArr'=>
                                            array(
                                                'notEmail'.$conditionKeySuffix=>array('zbsc_email','<>','%s',$condition['value']),
                                                'notEmailAka'.$conditionKeySuffix=>array('ID','NOT IN',"(SELECT aka_id FROM ".$ZBSCRM_t['aka']." WHERE aka_type = ".ZBS_TYPE_CONTACT." AND aka_alias = %s)",$condition['value'])
                                                )
                                        );
                            else if ($condition['operator'] == 'contains')
                                return array('additionalWhereArr'=>
                                            array('emailContains'.$conditionKeySuffix=>array("zbsc_email",'LIKE','%s','%'.$condition['value'].'%'))
                                        );
                            break;




                        // TBA (When DAL2 trans etc.)
                        case 'totalval': // 'equal','notequal','larger','less','floatrange'

                            break;

                        case 'dateadded': // 'before','after','daterange'

                            // contactedAfter
                            if ($condition['operator'] == 'before')
                                // while this is right, it doesn't allow for MULTIPLE status cond lines, so do via sql:
                                // return array('olderThan'=>$condition['value']);
                                return array('additionalWhereArr'=>
                                            array('olderThan'.$conditionKeySuffix=>array('zbsc_created','<=','%d',$condition['value']))
                                        );
                            else if ($condition['operator'] == 'after')
                                // while this is right, it doesn't allow for MULTIPLE status cond lines, so do via sql:
                                // return array('newerThan'=>$condition['value']);
                                return array('additionalWhereArr'=>
                                            array('newerThan'.$conditionKeySuffix=>array('zbsc_created','>=','%d',$condition['value']))
                                        );
                            else if ($condition['operator'] == 'daterange'){

                                $before = false; $after = false;
                                // split out the value 
                                if (isset($condition['value']) && !empty($condition['value'])) $after = (int)$condition['value'];
                                if (isset($condition['value2']) && !empty($condition['value2'])) $before = (int)$condition['value2'];

                                // while this is right, it doesn't allow for MULTIPLE status cond lines, so do via sql:
                                // return array('newerThan'=>$after,'olderThan'=>$before);
                                return array('additionalWhereArr'=>
                                            array(
                                                'newerThan'.$conditionKeySuffix=>array('zbsc_created','>=','%d',$condition['value']),
                                                'olderThan'.$conditionKeySuffix=>array('zbsc_created','<=','%d',$condition['value2'])
                                            )
                                        );

                            }

                            break;

                        case 'datelastcontacted': // 'before','after','daterange'

                            // contactedAfter
                            if ($condition['operator'] == 'before')
                                // while this is right, it doesn't allow for MULTIPLE status cond lines, so do via sql:
                                // return array('contactedBefore'=>$condition['value']);
                                return array('additionalWhereArr'=>
                                            array('contactedBefore'.$conditionKeySuffix=>array('zbsc_lastcontacted','<=','%d',$condition['value']))
                                        );
                            else if ($condition['operator'] == 'after')
                                // while this is right, it doesn't allow for MULTIPLE status cond lines, so do via sql:
                                // return array('contactedAfter'=>$condition['value']);
                                return array('additionalWhereArr'=>
                                            array('contactedAfter'.$conditionKeySuffix=>array('zbsc_lastcontacted','>=','%d',$condition['value']))
                                        );
                            else if ($condition['operator'] == 'daterange'){

                                $before = false; $after = false;
                                // split out the value 
                                if (isset($condition['value']) && !empty($condition['value'])) $after = (int)$condition['value'];
                                if (isset($condition['value2']) && !empty($condition['value2'])) $before = (int)$condition['value2'];

                                // while this is right, it doesn't allow for MULTIPLE status cond lines, so do via sql:
                                // return array('contactedAfter'=>$after,'contactedBefore'=>$before);
                                return array('additionalWhereArr'=>
                                            array(
                                                'contactedAfter'.$conditionKeySuffix=>array('zbsc_lastcontacted','>=','%d',$after),
                                                'contactedBefore'.$conditionKeySuffix=>array('zbsc_lastcontacted','<=','%d',$before)
                                            )
                                        );
                            }

                            break;

                        case 'tagged': // 'tag'

                            // while this is right, it doesn't allow for MULTIPLE status cond lines, so do via sql:
                            // return array('isTagged'=>$condition['value']);
                            // NOTE
                            // ... this is a DIRECT query, so format for adding here is a little diff
                            // ... and only works (not overriding existing ['direct']) because the calling func of this func has to especially copy separately
                            return array('additionalWhereArr'=>
                                            array('direct' => array(
                                                array('(SELECT COUNT(ID) FROM '.$ZBSCRM_t['taglinks'].' WHERE zbstl_objtype = %d AND zbstl_objid = contact.ID AND zbstl_tagid = %d) > 0',array(ZBS_TYPE_CONTACT,$condition['value']))
                                                )
                                            )
                                        );                        

                            break;

                        case 'nottagged': // 'tag'

                            // while this is right, it doesn't allow for MULTIPLE status cond lines, so do via sql:
                            // return array('isNotTagged'=>$condition['value']);

                            // NOTE
                            // ... this is a DIRECT query, so format for adding here is a little diff
                            // ... and only works (not overriding existing ['direct']) because the calling func of this func has to especially copy separately
                            return array('additionalWhereArr'=>
                                            array('direct' => array(
                                                array('(SELECT COUNT(ID) FROM '.$ZBSCRM_t['taglinks'].' WHERE zbstl_objtype = %d AND zbstl_objid = contact.ID AND zbstl_tagid = %d) = 0',array(ZBS_TYPE_CONTACT,$condition['value']))
                                                )
                                            )
                                        ); 
                            break;

                        default:

                            // Allow for custom segmentArgument builders
                            if (!empty($condition['type'])){
                                $filterTag = $this->makeSlug($condition['type']).'_zbsSegmentArgumentBuild';
                                $potentialArgs = apply_filters( $filterTag, false, $condition,$conditionKeySuffix );

                                // got anything back? 
                                if ($potentialArgs !== false) return $potentialArgs;
                            }

                            break;



                    }



                }

                return false;

           }

            // ONLY USED FOR SEGMENT SQL BUILING CURRENTLY, deep.
            // -----------------------
            // This was required to work with OR (e.g. postcode 1 = x or postcode 2 = x)
            // -----------------------
            // This generates a query like 'zbsc_fname LIKE %s OR zbsc_lname LIKE %s', 
            // which we then need to include as direct subquery
            public function segmentBuildDirectOrClause($directQueries=array(),$andOr='OR'){
            /* this works, in segmentConditionArgs(), adapted below to fit generic func to keep it DRY
                $conditionQArr = $this->buildWheres(array(
                                                    'email'.$conditionKeySuffix=>array('zbsc_email','=','%s',$condition['value']),
                                                    'emailAKA'.$conditionKeySuffix=>array('ID','IN',"(SELECT aka_id FROM ".$ZBSCRM_t['aka']." WHERE aka_type = ".ZBS_TYPE_CONTACT." AND aka_alias = %s)",$condition['value'])
                                                    ),'',array(),'OR',false);
                if (is_array($conditionQArr) && isset($conditionQArr['where']) && !empty($conditionQArr['where'])){                                    
                    return array('additionalWhereArr'=>array('direct'=>array(array('('.$conditionQArr['where'].')',$conditionQArr['params']))));
                }
                return array();

            */
                $directArr = $this->buildWheres($directQueries,'',array(),$andOr,false);
                if (is_array($directArr) && isset($directArr['where']) && !empty($directArr['where'])){                                    
                    return array('additionalWhereArr'=>array('direct'=>array(array('('.$directArr['where'].')',$directArr['params']))));
                }
                return array();
            }


           /**
             *  Compile a segment ()
             */
            public function compileSegment($segmentID=-1){

                if (!empty($segmentID)) {

                    // 'GET' the segment count without paging limits
                    // ... this func then automatically updates the compile record, so nothing to do :) 
                    return $this->getSegementAudience($segmentID,-1,-1,'ID','DESC',true);

                }

                return false;
            
           }


        /**
         * Takes full object and makes a "list view" boiled down version
         * Used to generate listview objs
         *
         * @param array $obj (clean obj)
         *
         * @return array (listview ready obj)
         */
        // This isn't used in segments, it was written PRE DAL3 so has it's own layer
        //public function listViewObj($segment=false,$columnsRequired=array()){return;}
        

    // =========== / SEGMENTS      ===================================================
    // ===============================================================================

} // / class
