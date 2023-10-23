## Description

The Bakery allows to create Word documents based on a configuration ("recipe") and input data. This is useful for creating
many docX documents with varying contents and length as they are build dynamically. It utilizes 2 main tools to achieve
this goal: phpWord to actually build the docX files and some features of the EDT libraries to help with data fetching.

Basically, the Bakery is a translation layer between data export configurations in a readable array format with dynamic
data input and the phpWord library.

## Installation

This is not yet figured out. I think, the Bakery only needs to get a composer package and than it should be usable just
like any other dependency. It is also already a Symfony bundle, which sets up nicely for additional conveniences.

WARNING: The autowiring might be rather messed up at the moment to allow for usage of EDT in the included tests. More
cleanup and streamlining is needed!

## Examples

For an example of the basic functionality, see the test setup and included recipe file (tests/resources/example_config.yml).

## How does it work internally?

The entry point  is the `create` method of the Bakery class. It only needs the name of the recipe (which needs to be
defined in the recipes .yml-file) and the necessary variables for the placeholders used in the recipe. Side Note:
placeholders can be used anywhere in the queries of the recipe.

The Bakery now takes the recipe and starts preparing the process of creating the final docx-output. Step one is to put all
relevant information from the recipe into the so called RecipeDataBag. It holds the basic queries, format information,
available styling and the list of instructions. In the second step the queries are used to already create the data fetchers
which are going to provide the dynamic data. This way, we have the sources of data already established and if something goes wrong,
we don't waste time and resources in the actual build (baking) process.

After those preparations, the RecipeProcessor will start iterating through the instructions to build the final output.
At this stage, static data will be used either directly from the instruction (if it was hardcoded there) or from a given
value in the recipe. Dynamic data will be loaded through the data fetchers from resource types representing the intended
data to be exported.

## Future Vision

There are a couple of areas that are not finished or will need work in the future. I'm going to add a list of what I consider
to be important to be used as a stepping stone for future work.

**Mandatory work**

1. Cleanup service wiring
I had to add a lot of manual wiring to get doctrine and EDT to work in the tests as I wanted them to. That wiring is massive,
messy and in the wrong place (services_edt.yml) but should be cleaned up and in the tests/resources/services.yml.

2. Exception Handling
At this point, there is basically no exception handling and validation. To make the Bakery more user friendly and robust,
that should be added very soon.

3. Update PHPWord
Since the first draft of the Bakery, PHPWord had a major version jump from 0.18 to 1.0 and then 1.1. As it is our primary
dependency, it should be kept up to date as much as possible.

4. Improve Unit-Testing
The test coverage is there, but not really good. It is more supposed to act as a somewhat useful placeholder. But it needs
major updates to be representative of the actual state of the library.

**Optional Work**

5. Consider decoupling from EDT. It is used primarily to allow for paginated continuous data loading, parsing of Drupal-style
filters and the convenience of accessing properties in a dot-notation (i.e. 'cookbook.name'). It should be at least reconsidered
if EDT is the right dependency for the job or if there are better alternatives out there.

## Glossary

#### Bakery

The Bakery is the entry point to the whole process as it only needs the name of the used recipe (the recipes have to be
defined previously) and the data necessary to fill in variables/placeholders. It is intended as the only public access to
the library to best encapsulate all the logic and make using it as easy as possible.

#### Recipe

Recipes comprise of some fundamental document data (format) and the structure of instructions to actually create the final output,
just like a baking recipe is a list of instructions/steps on how to prepare the finished product. They also include 2 more
information needed to accomplish that task: stylings to optionally modify the design of the instructions output and
queries (which are processed in data fetchers) that provide the dynamic data for instructions (think of this like adding
the actual ingredients in the baking process).

#### Instruction

Instructions are little commands that take some data (static from the recipe or
dynamic from a query) and create a bit of content in the generated docx file. There are some basic instructions in the library
at the moment, but there will still be some missing. These basic instructions are mostly representations of basic phpWord
instructions, but it is also very much possible and encouraged to create more complex instructions by combining basic ones.

The idea is also to easily allow projects using the bakery to create their own instructions and use them in their recipes.

#### Data Fetcher

A data fetcher gets created for every query defined in a recipe. They operate as the source of dynamic data that gets used
in the instructions by offering a bit of state management and handling the resource type with the needed filtering, sorting
and pagination.

#### Styles

Styles are - as you would expect - information about how the product of an instruction is supposed to look. They need to
be based on the available styling options of the used version of phpWord as they need to be translated/mapped to them.

You can define styles in 3 places: Globally to be accessible for all recipes, in any recipe to be only used in that particular
recipe or directly on an instruction to only affect that one instruction. Styles on a recipe or global level must be named
to be usable via their name in instructions.

