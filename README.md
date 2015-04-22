
# GridFieldSelectExisting

Silverstripe Gridfield component that allows you to manage relationships by ticking/unticking from a list of existing data objects

## Installation

`composer require danbroooks/gridfield-selectexisting`

## Usage

```php
$config = GridFieldConfig_RelationEditor::create(10)
	->addComponent(new GridFieldSelectExisting());

GridField::create('Relationship', 'Relationship', $this->Relationship(), $config));
```
