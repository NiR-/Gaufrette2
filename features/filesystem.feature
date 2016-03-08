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

    Scenario: read a directory
        Given a complex tree structure
        When I read directory "/complex/tree" content
        Then I should see the directory content

    Scenario: find all files and directories in a tree
        Given a complex tree structure
        When I search in directory "/complex/tree"
        Then I should see the complete tree
#
#    Scenario: find all files and directories following a pattern
#        Given a complex tree structure
#        When I search for pattern "*.txt" in directory "/complex/tree"
