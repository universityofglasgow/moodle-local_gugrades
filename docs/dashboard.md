# Accessing GuGrades data (for use on Dashboard)

Web services and/or API calls are provided to access the tool's data. It should not be necessary (and is discouraged) to interrogate the database tables directly. 
The functions provided are as follows..

## local_gugrades_dashboard_get_courses

This lists the courses in which the student is currently enrolled and shows if GCAT is enabled, GuGrades is enabled (or neither). If GuGrades is enabled it also
returns an array showing the top-level grade categories within that course (e.g. "Summative", "Formative"). The ids of these categories can then be used to make
further calls to access the grade data and sub-categories (if any)

#### Accessing the function

* Web services call:  `local_gugrades_dashboard_get_courses`
* Call to PHP API:  `\local_gugrades\api::dashboard_get_courses(int $userid)`

The web service call is AJAX enabled and can be called from Moodle Javascript using standard AJAX functions. If calling from other Moodle PHP, use the API function. 

#### Parameter

* int $userid - the userid for which courses are required. A user can either request their own course/grade data or a request can be made for a different user. This is controlled by capabilities. 

#### Returned data

The web service call returns the data specified by the web service definition (see automated documentation). The API call returns a very similar structure but complete database records are returned
so there are more items. 

Here is an example from the Web service call (which returns JSON)

    [
        {
            "id": 9,
            "shortname": "GTTWO",
            "fullname": "Grades Test TWO",
            "startdate": 1692399600,
            "enddate": 0,
            "gugradesenabled": false,
            "gcatenabled": false,
            "firstlevel": []
        },
        {
            "id": 4,
            "shortname": "GTEST",
            "fullname": "Grades test",
            "startdate": 1675987200,
            "enddate": 1690758000,
            "gugradesenabled": true,
            "gcatenabled": false,
            "firstlevel": [
                {
                    "id": 3,
                    "fullname": "Summative things"
                },
                {
                    "id": 4,
                    "fullname": "Formative things"
                }
            ]
        }
    ]

(Bash script that generated this is [here](../wstest.sh))

This returns data for two courses. The first has neither GCAT nor GuGrades enabled. The second has GuGrades enabled and therefore returns details of the top-level
grade categories. 

## local_gugrades_dashboard_get_grades