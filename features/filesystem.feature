@infrastructure
Feature: filesystem

    Scenario: read
        Given a file stored at "/a/path"
        When I ask for this file
        Then I should get the corresponding file object

    Scenario: write
        Given a file object for "/a/path"
        When I write it
        Then it should be stored

    Scenario: delete
        Given a file object for "/a/path"
        When I delete it
        Then it should be deleted

    Scenario: list
        Given there is a complex tree structure
        When I list
        Then I should see the complex tree structure
