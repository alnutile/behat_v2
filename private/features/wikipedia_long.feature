Feature: Testing WikiPedia

  Scenario: Lets go and look at the page
    Given I am on "http://en.wikipedia.org/wiki/Main_Page"
    Then I should see "Wiki"
    Then I should see "Wiki"
    And I wait
    Then I should see "Wiki"
    And I wait
    And I wait
    And I Wait