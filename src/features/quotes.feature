Feature: Quotes feature
  Scenario: Listing quotes from one author
    When I request "/shout/steve-jobs?limit=1" using HTTP GET
    Then the response code is 200
    And the response body is:
      """
      ["I AM THE BEST!"]
      """

  Scenario: Listing quotes with filter
    When I request "/shout/steve-jobs?limit=12" using HTTP GET
    Then the response code is 400
    And the response body is:
      """
      "Filter value should be equal or lower than 10 and higher than 0!"
      """

  Scenario: Listing quotes with no filter
    When I request "/shout/steve-jobs" using HTTP GET
    Then the response code is 400
    And the response body is:
      """
      "Filter value should be equal or lower than 10 and higher than 0!"
      """

  Scenario: Error when author is not found
    When I request "/shout/jesblu?limit=2" using HTTP GET
    Then the response code is 400
    And the response body is:
      """
      "Author not found."
      """
