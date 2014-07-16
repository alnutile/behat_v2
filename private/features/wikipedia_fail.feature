Feature: Testing WikiPedia

  Scenario: Lets go and look at the page
    Given I am on "http://en.wikipedia.org/wiki/Main_Page"
    Then I should see "Muffins"