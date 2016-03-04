@infrastructure
Feature: local files

    Scenario: read
        Given a file stored at "./a/path"
        When I ask for this file
        Then I should get the corresponding file object

    Scenario: write
        Given a file object for "./a/path"
        When I write it
        Then it should be stored

