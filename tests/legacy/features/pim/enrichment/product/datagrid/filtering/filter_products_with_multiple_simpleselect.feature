@javascript
Feature: Filter products with multiples simpleselect filters
  In order to filter products in the catalog
  As a regular user
  I need to be able to filter products with multiples simpleselect filters

  Background:
    Given the "default" catalog configuration
    And the following attributes:
      | code    | label-en_US | type                     | useable_as_grid_filter | group |
      | color   | Color       | pim_catalog_simpleselect | 1                      | other |
      | company | Company     | pim_catalog_simpleselect | 1                      | other |
    And the following family:
      | code      | attributes    |
      | furniture | color,company |
      | library   | color,company |
    And the following "color" attribute options: Black and Green
    And the following "company" attribute options: Debian and Canonical and Suze
    And the following products:
      | sku    | family    | company   | color |
      | BOOK   | library   |           |       |
      | MUG-1  | furniture | Canonical | Green |
      | MUG-2  | furniture | Suze      | Green |
      | MUG-3  | furniture | Debian    | Green |
      | MUG-4  | furniture | Canonical | Black |
      | MUG-5  | furniture | Suze      | Black |
      | POST-1 | furniture | Suze      |       |
      | POST-2 | furniture | Canonical |       |
      | POST-3 | furniture | Debian    |       |
    And I am logged in as "Mary"

  @critical
  Scenario: Successfully filter products with the same attributes
    Given I am on the products grid
    And I show the filter "company"
    And I filter by "company" with operator "in list" and value "Suze"
    And I show the filter "color"
    And I filter by "color" with operator "in list" and value "Green"
    Then the grid should contain 1 elements
    And I should see entities "MUG-2"
    And I hide the filter "company"
    And I hide the filter "color"
