<?php namespace Manager\EducationGroup\SmartGroups\Rules;
/**
 * Package admin for autopopulating rules.
 * This "class" will provide the public API for loading and calculating groups
 * ------------------------------------------------------------------------------------------------
 * Rules:
 *
 * - Each rule comes with it's own loading mechanizem (set in the class).
 * - params - param name should be concise
 *            redis key is combo of rule name + param name + value
 *            param should describe what it is for, not what it belongs to or what
 *            data field it is working on, ex. organization_id, hired_date, etc. note: date_created is a db system field and should not be used  as a param name
 *
 * ------------------------------------------------------------------------------------------------
 * Loading:
 * To apply the rules to an empty group, i.e. build a group from scratch,
 * I need to load for each rule ALL the users that falls under this rule for
 * SET calculate the group personel.
 *
 * If a group has zero users in it, I still need to create it in Redis, but as en empty set.
 * Since Redis does not support this out of the box, I will need to do it with rbac_user_id=0
 *
 *
 * @author Itay Moav
 */
abstract class API{
    
    /**
     * This rule is used when there is a need to simply combine 
     * several groups of ids into one big group (duplicates are removed)
     * Which later more SET actions can be performed on
     * @return \SmartGroups\Rules\Union
     */
    static public function Union(string $human_name='',string $human_description=''):\SmartGroups\Rules\Union{
        return \SmartGroups\Rules\Union::construct($human_name,$human_description);
    }
    
  
    /**
     * Creates an arbitrary group 
     * 
     * @param array<int> $ids
     * @param bool $force_refresh
     * @return \SmartGroups\Rules\StaticSET
     */
    static public function StaticSET(
        array $ids=[],
        bool $force_refresh=false,
        string $human_name='',
        string $human_description=''):\SmartGroups\Rules\StaticSET{
            
            return \SmartGroups\Rules\StaticSET::construct($ids,$force_refresh,$human_name,$human_description);
    }
    
    /**
     *
     * @param string $employee_feed_source
     * @param bool $force_refresh
     * @return \Manager\EducationGroup\SmartGroups\Rules\FeedEmployees
     */
    #[\Manager\EducationGroup\SmartGroups\Attributes\APIParams(type: "string", name: "employee_feed_source")]
    static public function FeedEmployees(string $employee_feed_source='', $force_refresh=false):\Manager\EducationGroup\SmartGroups\Rules\FeedEmployees{
        //NOTICE! this param is necessary so the rule has some name in Redis. I am unsetting it in Loader so it does not break query
        $params = ['ruleName'=>'feedEmployees'];
        if($employee_feed_source){
            $params['employee_feed_source'] = $employee_feed_source;
        }
        return new \Manager\EducationGroup\SmartGroups\Rules\FeedEmployees($params,$force_refresh);
    }
    /**
     *
     * @param int $org_id
     * @param bool $force_refresh
     * @return \Manager\EducationGroup\SmartGroups\Rules\Org
     */
    #[\Manager\EducationGroup\SmartGroups\Attributes\APIParams(type: "int", name: "org_id")]
    static public function Org(int $org_id,bool $force_refresh=false):\Manager\EducationGroup\SmartGroups\Rules\Org{
        return new \Manager\EducationGroup\SmartGroups\Rules\Org(['org_id' => $org_id],$force_refresh);
    }
    
    /**
     *
     * @param int $dep_id
     * @param bool $force_refresh
     * @return \Manager\EducationGroup\SmartGroups\Rules\Dep
     */
    static public function Dep(int $dep_id,bool $force_refresh=false):\Manager\EducationGroup\SmartGroups\Rules\Dep{
        return new \Manager\EducationGroup\SmartGroups\Rules\Dep(['dep_id' => $dep_id],$force_refresh);
    }
    
    /**
     *
     * @param string $cost_center
     * @param bool $force_refresh
     * @return \Manager\EducationGroup\SmartGroups\Rules\CostCenter
     */
    static public function CostCenter(string $cost_center,bool $force_refresh=false):\Manager\EducationGroup\SmartGroups\Rules\CostCenter{
        return new \Manager\EducationGroup\SmartGroups\Rules\CostCenter(['cost_center' => $cost_center],$force_refresh);
    }
    
    /**
     *
     * @param string $business_unit
     * @param bool $force_refresh
     * @return \Manager\EducationGroup\SmartGroups\Rules\BusinessUnit
     */
    static public function BusinessUnit(string $business_unit,bool $force_refresh=false) : \Manager\EducationGroup\SmartGroups\Rules\BusinessUnit {
        return new \Manager\EducationGroup\SmartGroups\Rules\BusinessUnit(['business_unit' => $business_unit],$force_refresh);
    }
    
    /**
     *
     * @param int $group_id
     * @param bool $force_refresh groups are what is changing in this process, so we prevent Redis to cache Groups (by default ).
     * @return \Manager\EducationGroup\SmartGroups\Rules\Group
     */
    static public function Group(int $group_id,bool $force_refresh=true):\Manager\EducationGroup\SmartGroups\Rules\Group{
        return new \Manager\EducationGroup\SmartGroups\Rules\Group(['group_id' => $group_id],$force_refresh);
    }
    
    /**
     *
     * @param int $group_id
     * @param bool $force_refresh groups are what is changing in this process, so we prevent Redis to cache Groups (by default ).
     * @return \Manager\EducationGroup\SmartGroups\Rules\Group
     */
    static public function FirstHalfGroup(int $group_id,bool $force_refresh=true):\Manager\EducationGroup\SmartGroups\Rules\FirstHalfGroup{
        return new \Manager\EducationGroup\SmartGroups\Rules\FirstHalfGroup(['group_id' => $group_id],$force_refresh);
    }
    
    /**
     *
     * @param string $job_func_code
     * @param bool $force_refresh
     * @return \Manager\EducationGroup\SmartGroups\Rules\JobFunc
     */
    static public function JobFunction(string $job_func_code,bool $force_refresh=false):\Manager\EducationGroup\SmartGroups\Rules\JobFunc{
        return new \Manager\EducationGroup\SmartGroups\Rules\JobFunc(['job_func_code' => $job_func_code],$force_refresh);
    }
    
    /**
     *
     * @param string $job_func_code
     * @param bool $force_refresh
     * @return \Manager\EducationGroup\SmartGroups\Rules\FeedJobFunc
     */
    static public function FeedJobFunction(string $job_func_code,bool $force_refresh=false):\Manager\EducationGroup\SmartGroups\Rules\FeedJobFunc{
        return new \Manager\EducationGroup\SmartGroups\Rules\FeedJobFunc(['job_func_code' => $job_func_code],$force_refresh);
    }
    /**
     *
     * @param string $job_subfunc_code
     * @param bool $force_refresh
     * @return \Manager\EducationGroup\SmartGroups\Rules\JobSubFunc
     */
    #[\Manager\EducationGroup\SmartGroups\Attributes\APIParams(type: "string", name: "job_subfunc_code")]
    static public function JobSubfunction(string $job_subfunc_code,bool $force_refresh=false):\Manager\EducationGroup\SmartGroups\Rules\JobSubFunc{
        return new \Manager\EducationGroup\SmartGroups\Rules\JobSubFunc(['job_subfunc_code' => $job_subfunc_code],$force_refresh);
    }
    /**
     *
     * @param string $job_subfunc_code
     * @param bool $force_refresh
     * @return \Manager\EducationGroup\SmartGroups\Rules\FeedJobSubFunc
     */
    #[\Manager\EducationGroup\SmartGroups\Attributes\APIParams(type: "string", name: "job_subfunc_code")]
    static public function FeedJobSubfunction(string $job_subfunc_code,bool $force_refresh=false):\Manager\EducationGroup\SmartGroups\Rules\FeedJobSubFunc{
        return new \Manager\EducationGroup\SmartGroups\Rules\FeedJobSubFunc(['job_subfunc_code' => $job_subfunc_code],$force_refresh);
    }
    
    /**
     *
     * @param string $job_code
     * @param bool $force_refresh
     * @return \Manager\EducationGroup\SmartGroups\Rules\Job
     */
    #[\Manager\EducationGroup\SmartGroups\Attributes\APIParams(type: "string", name: "job_code")]
    static public function Job(string $job_code,bool $force_refresh=false):\Manager\EducationGroup\SmartGroups\Rules\Job{
        return new \Manager\EducationGroup\SmartGroups\Rules\Job(['job_code' => $job_code],$force_refresh);
    }
    
    /**
     * @param string $job_code
     * @param bool $force_refresh
     * @return \Manager\EducationGroup\SmartGroups\Rules\FeedJob
     */
    #[\Manager\EducationGroup\SmartGroups\Attributes\APIParams(type: "string", name: "job_code")]
    static public function FeedJob(string $job_code,bool $force_refresh=false):\Manager\EducationGroup\SmartGroups\Rules\FeedJob{
        return new \Manager\EducationGroup\SmartGroups\Rules\FeedJob(['job_code' => $job_code],$force_refresh);
    }
    
    /**
     *
     * @param string $job_family_area
     * @param bool $force_refresh
     * @return \Manager\EducationGroup\SmartGroups\Rules\FeedJobFamilyArea
     */
    static public function FeedJobFamilyArea(string $job_family_area,bool $force_refresh=false):\Manager\EducationGroup\SmartGroups\Rules\FeedJobFamilyArea{
        return new \Manager\EducationGroup\SmartGroups\Rules\FeedJobFamilyArea(['job_family_area' => $job_family_area],$force_refresh);
    }
    
    /**
     *
     * @param string $job_family_specialty
     * @param bool $force_refresh
     * @return \Manager\EducationGroup\SmartGroups\Rules\FeedJobFamilySpecialty
     */
    #[\Manager\EducationGroup\SmartGroups\Attributes\APIParams(type: "string", name: "job_family_specialty")]
    static public function FeedJobFamilySpecialty(string $job_family_specialty,bool $force_refresh=false):\Manager\EducationGroup\SmartGroups\Rules\FeedJobFamilySpecialty{
        return new \Manager\EducationGroup\SmartGroups\Rules\FeedJobFamilySpecialty(['job_family_specialty' => $job_family_specialty],$force_refresh);
    }
    
    /**
     *
     * @param string $job_family_tier
     * @param bool $force_refresh
     * @return \Manager\EducationGroup\SmartGroups\Rules\FeedJobFamilyTier1
     */
    static public function FeedJobFamilyTier1( string $job_family_tier,bool $force_refresh=false):\Manager\EducationGroup\SmartGroups\Rules\FeedJobFamilyTier1{
        return new \Manager\EducationGroup\SmartGroups\Rules\FeedJobFamilyTier1(['feed_job_family_tier' => $job_family_tier],$force_refresh);
    }
    
    /**
     *
     * @param string $job_family_tier
     * @param bool $force_refresh
     * @return \Manager\EducationGroup\SmartGroups\Rules\FeedJobFamilyTier2
     */
    static public function FeedJobFamilyTier2( string $job_family_tier,bool $force_refresh=false):\Manager\EducationGroup\SmartGroups\Rules\FeedJobFamilyTier2{
        return new \Manager\EducationGroup\SmartGroups\Rules\FeedJobFamilyTier2(['feed_job_family_tier' => $job_family_tier],$force_refresh);
    }
    
    /**
     * 
     * @param string $job_family
     * @param bool $force_refresh
     * @return \Manager\EducationGroup\SmartGroups\Rules\FeedJobFamily
     */
    static public function FeedJobFamily(string $job_family,bool $force_refresh=false):\Manager\EducationGroup\SmartGroups\Rules\FeedJobFamily{
        return new \Manager\EducationGroup\SmartGroups\Rules\FeedJobFamily(['job_family' => $job_family],$force_refresh);
    }
    
    /**
     *
     * @param string $employee_location_code
     * @param bool $force_refresh
     * @return \Manager\EducationGroup\SmartGroups\Rules\FeedEmployeeLocationCode
     */
    static public function FeedEmployeeLocationCode(string $employee_location_code,bool $force_refresh=false):\Manager\EducationGroup\SmartGroups\Rules\FeedEmployeeLocationCode{
        return new \Manager\EducationGroup\SmartGroups\Rules\FeedEmployeeLocationCode(['employee_location_code' => $employee_location_code],$force_refresh);
    }
    
    /**
     *
     * @param string $manager_level
     * @param bool $force_refresh
     * @return \Manager\EducationGroup\SmartGroups\Rules\ManagerLevel
     */
    static public function ManagerLevel(string $manager_level,bool $force_refresh=false):\Manager\EducationGroup\SmartGroups\Rules\ManagerLevel{
        return new \Manager\EducationGroup\SmartGroups\Rules\ManagerLevel(['manager_level' => $manager_level],$force_refresh);
    }
    
    /**
     *
     * @param int $org_id for the org where we want to user job
     * @param string $job_func_code
     * @param bool $force_refresh
     * @return \Manager\EducationGroup\SmartGroups\Rules\OrgJobFunc
     */
    static public function OrgJobFunction(int $org_id,string $job_func_code,bool $force_refresh=false):\Manager\EducationGroup\SmartGroups\Rules\OrgJobFunc{
        return new \Manager\EducationGroup\SmartGroups\Rules\OrgJobFunc(['org_id' => $org_id,'job_func_code' => $job_func_code],$force_refresh);
    }
    
    /**
     *
     * @param int $org_id for the org where we want to user job
     * @param string $job_subfunc_code
     * @param bool $force_refresh
     * @return \Manager\EducationGroup\SmartGroups\Rules\OrgJobSubFunc
     */
    static public function OrgJobSubfunction(int $org_id,string $job_subfunc_code,bool $force_refresh=false):\Manager\EducationGroup\SmartGroups\Rules\OrgJobSubFunc{
        return new \Manager\EducationGroup\SmartGroups\Rules\OrgJobSubFunc(['org_id' => $org_id,'job_subfunc_code' => $job_subfunc_code],$force_refresh);
    }
    
    /**
     *
     * @param int $org_id
     * @param string $job_code
     * @param bool $force_refresh
     * @return \Manager\EducationGroup\SmartGroups\Rules\OrgJob
     */
    static public function OrgJob(int $org_id,string $job_code,bool $force_refresh=false):\Manager\EducationGroup\SmartGroups\Rules\OrgJob{
        return new \Manager\EducationGroup\SmartGroups\Rules\OrgJob(['org_id' => $org_id,'job_code' => $job_code],$force_refresh);
    }
    
    /**
     *
     * @param int $org_id for the org where we want to user job
     * @param string $manager_level
     * @param bool $force_refresh
     * @return \Manager\EducationGroup\SmartGroups\Rules\OrgManagerLevel
     */
    static public function OrgManagerLevel(int $org_id,string $manager_level,bool $force_refresh=false):\Manager\EducationGroup\SmartGroups\Rules\OrgManagerLevel{
        return new \Manager\EducationGroup\SmartGroups\Rules\OrgManagerLevel(['org_id' => $org_id,'manager_level' => $manager_level],$force_refresh);
    }
    
    /**
     *
     * @param string $manager_level
     * @param bool $force_refresh
     * @return \Manager\EducationGroup\SmartGroups\Rules\FeedManagerLevel
     */
    static public function FeedManagerLevel(string $manager_level,bool $force_refresh=false):\Manager\EducationGroup\SmartGroups\Rules\FeedManagerLevel{
        return new \Manager\EducationGroup\SmartGroups\Rules\FeedManagerLevel(['manager_level' => $manager_level],$force_refresh);
    }

    /**
     * @param int $course_id
     * @param bool $force_refresh
     * @return \Manager\EducationGroup\SmartGroups\Rules\EnrolledToCourse
     */
    #[\Manager\EducationGroup\SmartGroups\Attributes\APIParams(type: "int", name: "course_id")]
    static public function EnrolledToCourse(int $course_id,bool $force_refresh=false):\Manager\EducationGroup\SmartGroups\Rules\EnrolledToCourse{
        return new \Manager\EducationGroup\SmartGroups\Rules\EnrolledToCourse(['course_id'=>$course_id],$force_refresh);
    }

        /**
     * @param int $course_id
     * @param bool $force_refresh
     * @return \Manager\EducationGroup\SmartGroups\Rules\EnrolledToCourseBeforeYearMonthDay
     */
    static public function EnrolledToCourseBeforeYearMonthDay(int $course_id,string $year,string $month,string $day,bool $force_refresh=false):\Manager\EducationGroup\SmartGroups\Rules\EnrolledToCourseBeforeYearMonthDay{
        $params = ['EnrolledToCourseBeforeDate__date_created' => "{$year}-{$month}-{$day}",'course_id'=>$course_id];
        return new \Manager\EducationGroup\SmartGroups\Rules\EnrolledToCourseBeforeYearMonthDay($params,$force_refresh);
    }
    
    /**
     *  Rule to add users X days after they finished a course
     * @param int $course_id
     * @param int $days_after_completion
     * @param bool $force_refresh
     * @return \Manager\EducationGroup\SmartGroups\Rules\FinishedCourseXDays
     */
    static public function FinishedCourseXDays(int $course_id,int $days_after_completion=0,bool $force_refresh=false):\Manager\EducationGroup\SmartGroups\Rules\FinishedCourseXDays{
        return new \Manager\EducationGroup\SmartGroups\Rules\FinishedCourseXDays(['course_id'=>$course_id,'days_after_completion'=>$days_after_completion],$force_refresh);
    }
    
    /**
     *  Rule to add users X months in a range they finished a course
     * @param int $course_id
     * @param int $months_after_completion_min
     * @param int $months_after_completion_max
     * @param bool $force_refresh
     * @return \Manager\EducationGroup\SmartGroups\Rules\CourseCompletionDateRangeXMonths
     */
    static public function CourseCompletionDateRangeXMonths(int $course_id,int $months_after_completion_min=0,int $months_after_completion_max = 0, bool $force_refresh=false):\Manager\EducationGroup\SmartGroups\Rules\CourseCompletionDateRangeXMonths{
        return new \Manager\EducationGroup\SmartGroups\Rules\CourseCompletionDateRangeXMonths(['course_id'=>$course_id,'months_after_completion_min'=>$months_after_completion_min,'months_after_completion_max'=>$months_after_completion_max],$force_refresh);
    }
    
    /**
     * @param int $course_id
     * @param string $from_date yyyy-mm-dd
     * @param string $to_date yyyy-mm-dd
     * @param bool $force_refresh
     * @return \Manager\EducationGroup\SmartGroups\Rules\FinishedCourseRange
     */
    static public function FinishedCourseRange(int $course_id,string $from_date='',string $to_date='',bool $force_refresh=false):\Manager\EducationGroup\SmartGroups\Rules\FinishedCourseRange{
        return new \Manager\EducationGroup\SmartGroups\Rules\FinishedCourseRange(['course_id'=>$course_id,'fromDate'=>$from_date,'toDate'=>$to_date],$force_refresh);
    }
    
    /**
     *
     * @param string $year  full year yyyy
     * @param string $month full month, with leading zeroes 01, 02....12
     * @param string $day   full day with leading zeroes 01,02...31
     * @param bool $force_refresh
     * @return \Manager\EducationGroup\SmartGroups\Rules\HiredAfterYearMonthDay
     */
    static public function HiredAfterYearMonthDay(string $year,string $month, string $day,bool $force_refresh=false):\Manager\EducationGroup\SmartGroups\Rules\HiredAfterYearMonthDay{
        $params = ['HiredAfterYearMonthDay_employee_hired_date' => "{$year}-{$month}-{$day}"];
        return new \Manager\EducationGroup\SmartGroups\Rules\HiredAfterYearMonthDay($params,$force_refresh);
    }
    
    /**
     * @param int $organiztion_id 
     * @param string $year  full year yyyy
     * @param string $month full month, with leading zeroes 01, 02....12
     * @param string $day   full day with leading zeroes 01,02...31
     * @param bool $force_refresh
     * @return \Manager\EducationGroup\SmartGroups\Rules\HiredOnAndAfterYearMonthDayInOrganization
     */
    static public function HiredOnAndAfterYearMonthDayInOrganization(int $organization_id, string $year,string $month, string $day,bool $force_refresh=false):\Manager\EducationGroup\SmartGroups\Rules\HiredOnAndAfterYearMonthDayInOrganization{
        $params = ['employee_hired_date' => "{$year}-{$month}-{$day}",'organization_id' => $organization_id];
        return new \Manager\EducationGroup\SmartGroups\Rules\HiredOnAndAfterYearMonthDayInOrganization($params,$force_refresh);
    }
    
    /**
     * @param string $employee_location_code
     * @param string $year  full year yyyy
     * @param string $month full month, with leading zeroes 01, 02....12
     * @param string $day   full day with leading zeroes 01,02...31
     * @param bool $force_refresh
     * @return \Manager\EducationGroup\SmartGroups\Rules\HiredOnAndAfterYearMonthDayWithLocationCode
     */
    static public function HiredOnAndAfterYearMonthDayWithLocationCode(string $employee_location_code, string $year,string $month, string $day,bool $force_refresh=false):\Manager\EducationGroup\SmartGroups\Rules\HiredOnAndAfterYearMonthDayWithLocationCode{
        $params = ['employee_hired_date' => "{$year}-{$month}-{$day}",'employee_location_code' => $employee_location_code];
        return new \Manager\EducationGroup\SmartGroups\Rules\HiredOnAndAfterYearMonthDayWithLocationCode($params,$force_refresh);
    }
    
    /**
     * @param string $year
     * @param string $month
     * @param string $day
     * @param bool $force_refresh
     * @return \Manager\EducationGroup\SmartGroups\Rules\HiredOnAndAfterYearMonthDay
     */
    static public function HiredOnAndAfterYearMonthDay(string $year,string $month, string $day,bool $force_refresh=false):\Manager\EducationGroup\SmartGroups\Rules\HiredOnAndAfterYearMonthDay{
        $params = ['HiredOnAndAfterYearMonthDay_employee_hired_date' => "{$year}-{$month}-{$day}"];
        return new \Manager\EducationGroup\SmartGroups\Rules\HiredOnAndAfterYearMonthDay($params,$force_refresh);
    }
    
    /**
     * Created in the LMS, this is not necessarilly hired 
     * 
     * @param string $year
     * @param string $month
     * @param string $day
     * @param bool $force_refresh
     * @return \Manager\EducationGroup\SmartGroups\Rules\RbacUserCreatedAfterYearMonthDay
     */
    static public function RbacUserCreatedAfterYearMonthDay(string $year,string $month, string $day,bool $force_refresh=false):\Manager\EducationGroup\SmartGroups\Rules\RbacUserCreatedAfterYearMonthDay{
        $params = ['date_created' => "{$year}-{$month}-{$day}"];
        return new \Manager\EducationGroup\SmartGroups\Rules\RbacUserCreatedAfterYearMonthDay($params,$force_refresh);
    }
    
    /**
     * Created in the LMS, this is not necessarilly hired
     *
     * @param string $year
     * @param string $month
     * @param string $day
     * @param bool $force_refresh
     * @return \Manager\EducationGroup\SmartGroups\Rules\RbacUserCreatedInORGAfterYearMonthDay
     */
    static public function RbacUserCreatedInORGAfterYearMonthDay(int $organization_id,string $year,string $month, string $day,bool $force_refresh=false):\Manager\EducationGroup\SmartGroups\Rules\RbacUserCreatedInORGAfterYearMonthDay{
        $params = ['organization_id'=>$organization_id,'date_created' => "{$year}-{$month}-{$day}"];
        return new \Manager\EducationGroup\SmartGroups\Rules\RbacUserCreatedInORGAfterYearMonthDay($params,$force_refresh);
    }
    
    /**
     * To arbitraily create a group/rule of users which is too big for adhoc lists (thousands)
     * I have a table I can put users there, and identifiy by group ID they should belong to
     * lms2groups.manual_users_in_auto_groups
     *
     * @param int $manual_populate_group_id
     * @param bool $force_refresh
     * @return \Manager\EducationGroup\SmartGroups\Rules\ManualUserList
     */
    static public function ManualUserList(int $manual_populate_group_id,bool $force_refresh=false):\Manager\EducationGroup\SmartGroups\Rules\ManualUserList{
        return new \Manager\EducationGroup\SmartGroups\Rules\ManualUserList(['manual_populate_group_id'=>$manual_populate_group_id],$force_refresh);
    }
    
    /**
     * @param string $year
     * @param string $month
     * @param string $day
     * @param bool $force_refresh
     * @return \Manager\EducationGroup\SmartGroups\Rules\DateBecameLeader
     */
    static public function DateBecameLeader(string $year,string $month, string $day,bool $force_refresh=false):\Manager\EducationGroup\SmartGroups\Rules\DateBecameLeader{
        $params = ['date_id' => "{$year}{$month}{$day}"];
        return new \Manager\EducationGroup\SmartGroups\Rules\DateBecameLeader($params,$force_refresh);
    }
    
    /**
     * Primary org and primary job
     * @param int $org_id
     * @param string $job_code
     * @param bool $force_refresh
     * @return \Manager\EducationGroup\SmartGroups\Rules\OrgJob
     */
    static public function PrimaryOrgJob(int $org_id,string $job_code,bool $force_refresh=false):\Manager\EducationGroup\SmartGroups\Rules\PrimaryOrgJob{
        return new \Manager\EducationGroup\SmartGroups\Rules\PrimaryOrgJob(['org_id' => $org_id,'job_code' => $job_code],$force_refresh);
    }
    
    
    /**
     * @param string $primary_feed_job_code
     * @param bool $force_refresh
     * @return \Manager\EducationGroup\SmartGroups\Rules\PrimaryFeedJob
     */
    #[\Manager\EducationGroup\SmartGroups\Attributes\APIParams(type: "string", name: "primary_feed_job_code")]
    static public function PrimaryFeedJob(string $primary_feed_job_code,bool $force_refresh=false):\Manager\EducationGroup\SmartGroups\Rules\PrimaryFeedJob{
        return new \Manager\EducationGroup\SmartGroups\Rules\PrimaryFeedJob(['primary_feed_job_code' => $primary_feed_job_code],$force_refresh);
    }
    
    /**
     *
     * @param string $job_family_area
     * @param bool $force_refresh
     * @return \Manager\EducationGroup\SmartGroups\Rules\PrimaryFeedJobFamilyArea
     */
    static public function PrimaryFeedJobFamilyArea(string $job_family_area,bool $force_refresh=false):\Manager\EducationGroup\SmartGroups\Rules\PrimaryFeedJobFamilyArea{
        return new \Manager\EducationGroup\SmartGroups\Rules\PrimaryFeedJobFamilyArea(['primary_feed_job_family_area' => $job_family_area],$force_refresh);
    }
    
    /**
     *
     * @param string $job_family_specialty
     * @param bool $force_refresh
     * @return \Manager\EducationGroup\SmartGroups\Rules\PrimaryFeedJobFamilySpecialty
     */
    #[\Manager\EducationGroup\SmartGroups\Attributes\APIParams(type: "string", name: "primary_feed_job_family_specialty")]
    static public function PrimaryFeedJobFamilySpecialty(string $primary_feed_job_family_specialty,bool $force_refresh=false):\Manager\EducationGroup\SmartGroups\Rules\PrimaryFeedJobFamilySpecialty{
        return new \Manager\EducationGroup\SmartGroups\Rules\PrimaryFeedJobFamilySpecialty(['primary_feed_job_family_specialty' => $primary_feed_job_family_specialty],$force_refresh);
    }
    
    /**
     *
     * @param string $job_family_tier
     * @param bool $force_refresh
     * @return \Manager\EducationGroup\SmartGroups\Rules\PrimaryFeedJobFamilyTier1
     */
    static public function PrimaryFeedJobFamilyTier1( string $job_family_tier,bool $force_refresh=false):\Manager\EducationGroup\SmartGroups\Rules\PrimaryFeedJobFamilyTier1{
        return new \Manager\EducationGroup\SmartGroups\Rules\PrimaryFeedJobFamilyTier1(['primary_feed_job_family_tier' => $job_family_tier],$force_refresh);
    }
    
    /**
     *
     * @param string $job_family_tier
     * @param bool $force_refresh
     * @return \Manager\EducationGroup\SmartGroups\Rules\PrimaryFeedJobFamilyTier2
     */
    static public function PrimaryFeedJobFamilyTier2( string $job_family_tier,bool $force_refresh=false):\Manager\EducationGroup\SmartGroups\Rules\PrimaryFeedJobFamilyTier2{
        return new \Manager\EducationGroup\SmartGroups\Rules\PrimaryFeedJobFamilyTier2(['primary_feed_job_family_tier' => $job_family_tier],$force_refresh);
    }
    
    /**
     *
     * @param string $job_family_tier
     * @param bool $force_refresh
     * @return \Manager\EducationGroup\SmartGroups\Rules\PrimaryFeedJobFamilyTier3
     */
    static public function PrimaryFeedJobFamilyTier3( string $job_family_tier,bool $force_refresh=false):\Manager\EducationGroup\SmartGroups\Rules\PrimaryFeedJobFamilyTier3{
        return new \Manager\EducationGroup\SmartGroups\Rules\PrimaryFeedJobFamilyTier3(['primary_feed_job_family_tier' => $job_family_tier],$force_refresh);
    }
    
    /**
     *
     * @param string $job_family_tier
     * @param bool $force_refresh
     * @return \Manager\EducationGroup\SmartGroups\Rules\PrimaryFeedJobFamilyTier4
     */
    static public function PrimaryFeedJobFamilyTier4( string $job_family_tier,bool $force_refresh=false):\Manager\EducationGroup\SmartGroups\Rules\PrimaryFeedJobFamilyTier4{
        return new \Manager\EducationGroup\SmartGroups\Rules\PrimaryFeedJobFamilyTier4(['primary_feed_job_family_tier' => $job_family_tier],$force_refresh);
    }
    
    /**
     *
     * @param string $job_family_tier
     * @param bool $force_refresh
     * @return \Manager\EducationGroup\SmartGroups\Rules\PrimaryFeedJobFamilyTier5
     */
    static public function PrimaryFeedJobFamilyTier5( string $job_family_tier,bool $force_refresh=false):\Manager\EducationGroup\SmartGroups\Rules\PrimaryFeedJobFamilyTier5{
        return new \Manager\EducationGroup\SmartGroups\Rules\PrimaryFeedJobFamilyTier5(['primary_feed_job_family_tier' => $job_family_tier],$force_refresh);
    }
    
    /**
     *
     * @param string $employee_location_code
     * @param bool $force_refresh
     * @return \Manager\EducationGroup\SmartGroups\Rules\PrimaryFeedEmployeeLocationCode
     */
    static public function PrimaryFeedEmployeeLocationCode(string $employee_location_code,bool $force_refresh=false):\Manager\EducationGroup\SmartGroups\Rules\PrimaryFeedEmployeeLocationCode{
        return new \Manager\EducationGroup\SmartGroups\Rules\PrimaryFeedEmployeeLocationCode(['primary_feed_employee_location_code' => $employee_location_code],$force_refresh);
    }
    
    /**
     *
     * @param string $year  full year yyyy
     * @param string $month full month, with leading zeroes 01, 02....12
     * @param string $day   full day with leading zeroes 01,02...31
     * @param bool $force_refresh
     * @return \Manager\EducationGroup\SmartGroups\Rules\PrimaryFeedHiredAfterYearMonthDay
     */
    static public function PrimaryFeedHiredAfterYearMonthDay(string $year,string $month, string $day,bool $force_refresh=false):\Manager\EducationGroup\SmartGroups\Rules\PrimaryFeedHiredAfterYearMonthDay{
        $params = ['primary_feed_employee_hired_date' => "{$year}-{$month}-{$day}"];
        return new \Manager\EducationGroup\SmartGroups\Rules\PrimaryFeedHiredAfterYearMonthDay($params,$force_refresh);
    }
    
    /**
     *
     * @param string $manager_level
     * @param bool $force_refresh
     * @return \Manager\EducationGroup\SmartGroups\Rules\PrimaryFeedManagerLevel
     */
    static public function PrimaryFeedManagerLevel(string $manager_level,bool $force_refresh=false):\Manager\EducationGroup\SmartGroups\Rules\PrimaryFeedManagerLevel{
        return new \Manager\EducationGroup\SmartGroups\Rules\PrimaryFeedManagerLevel(['primary_feed_manager_level' => $manager_level],$force_refresh);
    }
    
    /**
     *
     * @param string $business_unit
     * @param bool $force_refresh
     * @return \Manager\EducationGroup\SmartGroups\Rules\PrimaryFeedBusinessUnit
     */
    static public function PrimaryFeedBusinessUnit(string $business_unit,bool $force_refresh=false) : \Manager\EducationGroup\SmartGroups\Rules\PrimaryFeedBusinessUnit {
        return new \Manager\EducationGroup\SmartGroups\Rules\PrimaryFeedBusinessUnit(['primary_feed_business_unit' => $business_unit],$force_refresh);
    }
    /**
     *
     * @param string $primary_or_secondary_feed_business_unit
     * @param bool $force_refresh
     * @return \Manager\EducationGroup\SmartGroups\Rules\FeedBusinessUnit
     */
    static public function FeedBusinessUnit(string $business_unit,bool $force_refresh=false) : \Manager\EducationGroup\SmartGroups\Rules\FeedBusinessUnit {
        return new \Manager\EducationGroup\SmartGroups\Rules\FeedBusinessUnit(['primary_or_secondary_feed_business_unit' => $business_unit],$force_refresh);
    }
    
    
    /**
     *
     * @param bool $force_refresh
     * @return \Manager\EducationGroup\SmartGroups\Rules\PrimaryFeedEmployees
     */
    static public function PrimaryFeedEmployees(string $employee_feed_source='', $force_refresh=false):\Manager\EducationGroup\SmartGroups\Rules\PrimaryFeedEmployees{
        //NOTICE! this param is necessary so the rule has some name in Redis. I am unsetting it in Loader so it does not break query
        $params = ['ruleName'=>'PrimaryFeedEmployees'];
        if($employee_feed_source){
            $params['primary_employee_feed_source'] = $employee_feed_source;
        }
        return new \Manager\EducationGroup\SmartGroups\Rules\PrimaryFeedEmployees($params,$force_refresh);
    }
    
    /**
     *
     * @param string $job_func_code
     * @param bool $force_refresh
     * @return \Manager\EducationGroup\SmartGroups\Rules\PrimaryFeedJobFunc
     */
    static public function PrimaryFeedJobFunction(string $job_func_code,bool $force_refresh=false):\Manager\EducationGroup\SmartGroups\Rules\PrimaryFeedJobFunc{
        return new \Manager\EducationGroup\SmartGroups\Rules\PrimaryFeedJobFunc(['primary_feed_job_func_code' => $job_func_code],$force_refresh);
    }
    
    /**
     *
     * @param string $year
     * @param string $month
     * @param string $day
     * @param bool $force_refresh
     * @return \Manager\EducationGroup\SmartGroups\Rules\HiredBeforeYearMonthDay
     */
    static public function HiredBeforeYearMonthDay(string $year,string $month, string $day,bool $force_refresh=false):\Manager\EducationGroup\SmartGroups\Rules\HiredBeforeYearMonthDay{
        $params = ['HiredBeforeYearMonthDay_employee_hired_date' => "{$year}-{$month}-{$day}"];
        return new \Manager\EducationGroup\SmartGroups\Rules\HiredBeforeYearMonthDay($params,$force_refresh);
    }   
    
    /**
     * 
     * @param string $date_created accepts year-month-day (yyyy-mm-dd) or yearmonthday (yyyymmdd)
     * @param bool $force_refresh
     * @return \Manager\EducationGroup\SmartGroups\Rules\RbacUserDateCreatedEquals
     */
    #[\Manager\EducationGroup\SmartGroups\Attributes\APIParams(type: "string", name: "date_created")]
    static public function RbacUserDateCreatedEquals(string $date_created,bool $force_refresh=false):\Manager\EducationGroup\SmartGroups\Rules\RbacUserDateCreatedEquals{
        $params = ['date_created' => intval(date('Ymd', strtotime($date_created)))]; // Convert year-month-date to yearmonthdate for dim_user table
        return new \Manager\EducationGroup\SmartGroups\Rules\RbacUserDateCreatedEquals($params,$force_refresh);
    }
    
    /**
     *
     * @param string $date_created year-month-day accepts year-month-day (yyyy-mm-dd) or yearmonthday (yyyymmdd)
     * @param bool $force_refresh
     * @return \Manager\EducationGroup\SmartGroups\Rules\RbacUserDateCreatedGreaterThanEquals
     */
    #[\Manager\EducationGroup\SmartGroups\Attributes\APIParams(type: "string", name: "date_created")]
    static public function RbacUserDateCreatedGreaterThanEquals(string $date_created,bool $force_refresh=false):\Manager\EducationGroup\SmartGroups\Rules\RbacUserDateCreatedGreaterThanEquals{
        $params = ['date_created' => intval(date('Ymd', strtotime($date_created)))]; // Convert year-month-date to yearmonthdate for dim_user table
        return new \Manager\EducationGroup\SmartGroups\Rules\RbacUserDateCreatedGreaterThanEquals($params,$force_refresh);
    }
    
    /**
     *
     * @param string $date_created_start year-month-day accepts year-month-day (yyyy-mm-dd) or yearmonthday (yyyymmdd)
     * @param string $date_created_end year-month-day accepts year-month-day (yyyy-mm-dd) or yearmonthday (yyyymmdd)
     * @param bool $force_refresh
     * @return \Manager\EducationGroup\SmartGroups\Rules\RbacUserDateCreatedBetween
     */
    #[\Manager\EducationGroup\SmartGroups\Attributes\APIParams(type: "string", name: "date_created_start")]
    #[\Manager\EducationGroup\SmartGroups\Attributes\APIParams(type: "string", name: "date_created_end")]
    static public function RbacUserDateCreatedBetween(string $date_created_start,string $date_created_end,bool $force_refresh=false):\Manager\EducationGroup\SmartGroups\Rules\RbacUserDateCreatedBetween{
        $params = [
            'date_created_start'    => intval(date('Ymd', strtotime($date_created_start))), // Convert year-month-date to yearmonthdate for dim_user table
            'date_created_end'      => intval(date('Ymd', strtotime($date_created_end)))
        ];
        return new \Manager\EducationGroup\SmartGroups\Rules\RbacUserDateCreatedBetween($params,$force_refresh);
    }
    
    /**
     *
     * @param int $course_id
     * @param string $date_completed year-month-day accepts only year-month-day (yyyy-mm-dd)
     * @param bool $force_refresh
     * @return \Manager\EducationGroup\SmartGroups\Rules\CourseCompletionDateEquals
     */
    #[\Manager\EducationGroup\SmartGroups\Attributes\APIParams(type: "int", name: "course_id")]
    #[\Manager\EducationGroup\SmartGroups\Attributes\APIParams(type: "string", name: "date_completed")]
    static public function CourseCompletionDateEquals(int $course_id, string $date_completed,bool $force_refresh=false):\Manager\EducationGroup\SmartGroups\Rules\CourseCompletionDateEquals{
        $params = [
            'course_id'      => $course_id,
            'date_completed' => $date_completed
        ];
        return new \Manager\EducationGroup\SmartGroups\Rules\CourseCompletionDateEquals($params,$force_refresh);
    }
    
    /**
     *
     * @param int $course_id
     * @param string $date_completed year-month-day accepts only year-month-day (yyyy-mm-dd)
     * @param bool $force_refresh
     * @return \Manager\EducationGroup\SmartGroups\Rules\CourseCompletionDateGreaterThanEquals
     */
    #[\Manager\EducationGroup\SmartGroups\Attributes\APIParams(type: "int", name: "course_id")]
    #[\Manager\EducationGroup\SmartGroups\Attributes\APIParams(type: "string", name: "date_completed")]
    static public function CourseCompletionDateGreaterThanEquals(int $course_id, string $date_completed,bool $force_refresh=false):\Manager\EducationGroup\SmartGroups\Rules\CourseCompletionDateGreaterThanEquals{
        $params = [
            'course_id'      => $course_id,
            'date_completed' => $date_completed
        ];
        return new \Manager\EducationGroup\SmartGroups\Rules\CourseCompletionDateGreaterThanEquals($params,$force_refresh);
    }
    
    /**
     *
     * @param int $course_id
     * @param string $date_completed_start year-month-day accepts only year-month-day (yyyy-mm-dd)
     * @param string $date_completed_end year-month-day accepts only year-month-day (yyyy-mm-dd)
     * @param bool $force_refresh
     * @return \Manager\EducationGroup\SmartGroups\Rules\CourseCompletionDateBetween
     */
    #[\Manager\EducationGroup\SmartGroups\Attributes\APIParams(type: "int", name: "course_id")]
    #[\Manager\EducationGroup\SmartGroups\Attributes\APIParams(type: "string", name: "date_completed_start")]
    #[\Manager\EducationGroup\SmartGroups\Attributes\APIParams(type: "string", name: "date_completed_end")]
    static public function CourseCompletionDateBetween(int $course_id, string $date_completed_start,string $date_completed_end,bool $force_refresh=false):\Manager\EducationGroup\SmartGroups\Rules\CourseCompletionDateBetween{
        $params = [
            'course_id'               => $course_id,
            'date_completed_start'    => $date_completed_start,
            'date_completed_end'      => $date_completed_end
        ];
        return new \Manager\EducationGroup\SmartGroups\Rules\CourseCompletionDateBetween($params,$force_refresh);
    }

    /**
     * TOBEDELETED20250510 - this rule is not ready to be used. either delete it in the future or leave it be
     * 
     * @param int $limit_users_val
     * @param int $offset_multiplier
     * @param int $phase_into_group_id
     * @param int $phase_from_group_id
     * @param bool $force_refresh
     * @return \Manager\EducationGroup\SmartGroups\Rules\PhaseGroupUsersToTargetGroup
     */
    static public function PhaseGroupUsersToTargetGroup(int $limit_users_val, int $offset_multiplier, int $phase_into_group_id, int $phase_from_group_id, $force_refresh=false):\Manager\EducationGroup\SmartGroups\Rules\PhaseGroupUsersToTargetGroup {
        $params = [
            'limit_users_val'               => $limit_users_val,
            'offset_multiplier'             => $offset_multiplier, // tobecalculated in the loader
            'phase_into_group_id'           => $phase_into_group_id,
            'phase_from_group_id'           => $phase_from_group_id
        ];
        return new \Manager\EducationGroup\SmartGroups\Rules\PhaseGroupUsersToTargetGroup($params,$force_refresh);
    }
}