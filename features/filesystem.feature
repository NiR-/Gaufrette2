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

    Scenario: list directory content
        Given a complex tree structure
        When I list directory "/complex/tree" content
        Then I should see the directory content

    Scenario: find
        Given a complex tree structure
        When I search
        Then I should see the complex tree structure
