@gradingform @gradingform_rubric @javascript
Feature: Verifying rubric scale conversion to grades
  In order to verify that rubric scores using a scale are correctly converted to grades
  As a teacher
  I need to ensure that grading with a rubric and a scale produces the expected grade outcome

  Scenario: Convert form rubric scores with a scale to grades.
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1 | topics |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And the following "scales" exist:
      | name         | scale                                     |
      | Test scale 1 | Disappointing, Good, Very good, Excellent |
    And the following "activities" exist:
      | activity   | name         | intro | course | idnumber    | grade   | advancedgradingmethod_submissions |
      | forum      | Test forum 1 | Test  | C1     | assign1     | 100     | rubric                            |
    And I change window size to "large"
    And I am on the "Test forum 1" "forum activity editing" page logged in as teacher1
    And I expand all fieldsets
    And I set the following fields to these values:
      | Whole forum grading > Type   | Scale                     |
      | Whole forum grading > Scale  | Default competence scale  |
      | Grading method               | Rubric                    |
    And I press "Save and display"
    And I go to "Test forum 1" advanced grading definition page
    # Defining a rubric.
    And I set the following fields to these values:
      | Name | Forum 1 rubric |
      | Description | Rubric test description |
    And I define the following rubric:
      | Criterion 1 | Level 11 | 1  | Level 12 | 20 | Level 13 | 40 | Level 14  | 50  |
      | Criterion 2 | Level 21 | 10 | Level 22 | 20 | Level 23 | 30 |           |     |
      | Criterion 3 | Level 31 | 5  | Level 32 | 20 |          |    |           |     |
    And I press "Save rubric and make it ready"
    When I am on the "Test forum 1" "forum activity" page logged in as teacher1
    And I press "Grade users"
    And I click on "Level 14 50 points" "radio"
    And I click on "Level 23 30 points" "radio"
    And I click on "Level 32 20 points" "radio"
    And I press "Save"
    And I am on the "Course 1" "grades > Grader report > View" page
    Then I should see "Competent" in the "student1@example.com" "table_row"
