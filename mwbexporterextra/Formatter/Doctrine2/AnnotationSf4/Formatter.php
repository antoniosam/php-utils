<?php


namespace Ast\MwbExporterExtra\Formatter\Doctrine2\AnnotationSf4;

use MwbExporter\Formatter\Doctrine2\Formatter as BaseFormatter;
use MwbExporter\Model\Base;
use MwbExporter\Validator\ChoiceValidator;

class Formatter extends BaseFormatter
{
    const CFG_ANNOTATION_PREFIX              = 'useAnnotationPrefix';
    const CFG_EXTENDS_CLASS                  = 'extendsClass';
    const CFG_PROPERTY_TYPEHINT              = 'propertyTypehint';
    const CFG_SKIP_GETTER_SETTER             = 'skipGetterAndSetter';
    const CFG_GENERATE_ENTITY_SERIALIZATION  = 'generateEntitySerialization';
    const CFG_GENERATE_EXTENDABLE_ENTITY     = 'generateExtendableEntity';
    const CFG_QUOTE_IDENTIFIER_STRATEGY      = 'quoteIdentifierStrategy';

    const QUOTE_IDENTIFIER_AUTO              = 'auto';
    const QUOTE_IDENTIFIER_ALWAYS            = 'always';
    const QUOTE_IDENTIFIER_NONE              = 'none';

    protected function init()
    {
        parent::init();
        $this->addConfigurations(array(
            static::CFG_INDENTATION                     => 4,
            static::CFG_FILENAME                        => '%entity%.%extension%',
            static::CFG_ANNOTATION_PREFIX               => 'ORM\\',
            static::CFG_SKIP_GETTER_SETTER              => false,
            static::CFG_GENERATE_ENTITY_SERIALIZATION   => true,
            static::CFG_GENERATE_EXTENDABLE_ENTITY      => false,
            static::CFG_QUOTE_IDENTIFIER_STRATEGY       => static::QUOTE_IDENTIFIER_AUTO,
            static::CFG_EXTENDS_CLASS                   => '',
            static::CFG_PROPERTY_TYPEHINT               => false,
            static::CFG_SKIP_PLURAL                     =>true
        ));
        $this->addValidators(array(
            static::CFG_QUOTE_IDENTIFIER_STRATEGY       => new ChoiceValidator(array(
                static::QUOTE_IDENTIFIER_AUTO,
                static::QUOTE_IDENTIFIER_ALWAYS,
                static::QUOTE_IDENTIFIER_NONE,
            )),
        ));
    }

    /**
     * (non-PHPdoc)
     * @see \MwbExporter\Formatter\Formatter::createDatatypeConverter()
     */
    protected function createDatatypeConverter()
    {
        return new DatatypeConverter();
    }

    /**
     * (non-PHPdoc)
     * @see \MwbExporter\Formatter\Formatter::createTable()
     */
    public function createTable(Base $parent, $node)
    {
        return new Model\Table($parent, $node);
    }

    /**
     * (non-PHPdoc)
     * @see \MwbExporter\Formatter\FormatterInterface::createColumn()
     */
    public function createColumn(Base $parent, $node)
    {
        return new Model\Column($parent, $node);
    }

    public function getTitle()
    {
        return 'Doctrine 2.0 Annotation Classes Symfony 4';
    }

    public function getFileExtension()
    {
        return 'php';
    }
}