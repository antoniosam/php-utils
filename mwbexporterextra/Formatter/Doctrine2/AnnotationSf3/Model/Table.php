<?php

/*
 * The MIT License
 *
 * Copyright (c) 2010 Johannes Mueller <circus2(at)web.de>
 * Copyright (c) 2012-2014 Toha <tohenk@yahoo.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace Ast\MwbExporterExtra\Formatter\Doctrine2\AnnotationSf3\Model;

use Ast\MwbExporterExtra\Formatter\Doctrine2\AnnotationSf3\Formatter;

use MwbExporter\Formatter\Doctrine2\Annotation\Model\Table as BaseTable;

use MwbExporter\Model\ForeignKey;
use MwbExporter\Object\Annotation;
use MwbExporter\Writer\WriterInterface;
use MwbExporter\Helper\Comment;
use MwbExporter\Helper\ReservedWords;
use Doctrine\Common\Inflector\Inflector;

class Table extends BaseTable
{

    protected function writeEntity(WriterInterface $writer)
    {
        $this->getDocument()->addLog(sprintf('Writing table "%s"', $this->getModelName()));

        $namespace = $this->getEntityNamespace();
        if ($repositoryNamespace = $this->getConfig()->get(Formatter::CFG_REPOSITORY_NAMESPACE)) {
            $repositoryNamespace .= '\\';
        }
        $skipGetterAndSetter = $this->getConfig()->get(Formatter::CFG_SKIP_GETTER_SETTER);
        $serializableEntity  = $this->getConfig()->get(Formatter::CFG_GENERATE_ENTITY_SERIALIZATION);
        $extendableEntity    = $this->getConfig()->get(Formatter::CFG_GENERATE_EXTENDABLE_ENTITY);
        $lifecycleCallbacks  = $this->getLifecycleCallbacks();

        $extendsClass = $this->getClassToExtend();
        $implementsInterface = $this->getInterfaceToImplement();

        $comment = $this->getComment();
        $writer
            ->open($this->getClassFileName($extendableEntity ? true : false))
            ->write('<?php')
            ->write('')
            ->writeCallback(function(WriterInterface $writer, Table $_this = null) {
                if ($_this->getConfig()->get(Formatter::CFG_ADD_COMMENT)) {
                    $writer
                        ->write($_this->getFormatter()->getComment(Comment::FORMAT_PHP))
                        ->write('')
                    ;
                }
            })
            ->write('namespace %s;', $namespace)
            ->write('')
            ->writeCallback(function(WriterInterface $writer, Table $_this = null) {
                $_this->writeUsedClasses($writer);
            });
        foreach ($this->getTableIndices() as $index) {
            if($index->isUnique()){
                $writer->write('use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;');
                break;
            }
        }
        if($this->tableisAuthentication()){
            $writer->write('use Symfony\Component\Security\Core\User\AdvancedUserInterface;');
            $writer->write('use Symfony\Component\Security\Core\User\UserInterface;') ;

        }

        $writer
            ->write('')
            ->write('/**')
            ->write(' * '.$this->getNamespace(null, false))
            ->write(' *')
            ->writeIf($comment, $comment)
            ->write(' * '.$this->getAnnotation('Entity', array('repositoryClass' => $this->getConfig()->get(Formatter::CFG_AUTOMATIC_REPOSITORY) ? $repositoryNamespace.$this->getModelName().'Repository' : null)));
        foreach ($this->getTableIndices() as $index) {
            if($index->isUnique()){
                foreach ($index->getColumns() as $column) {
                    $writer->write( ' * '.new Annotation("@UniqueEntity",array("fields"=>$column->getColumnName(),"message"=>ucfirst($column->getColumnName()).' ya esta en uso')));
                }
            }
        }
        $writer->write(' * '.$this->getAnnotation('Table', array('name' => $this->quoteIdentifier($this->getRawTableName(),false), 'indexes' => $this->getIndexesAnnotation('Index'), 'uniqueConstraints' => $this->getIndexesAnnotation('UniqueConstraint'))))
            ->writeIf($extendableEntity, ' * '.$this->getAnnotation('InheritanceType', array('SINGLE_TABLE')))
            ->writeIf($extendableEntity, ' * '.$this->getAnnotation('DiscriminatorColumn', $this->getInheritanceDiscriminatorColumn()))
            ->writeIf($extendableEntity, ' * '.$this->getAnnotation('DiscriminatorMap', array($this->getInheritanceDiscriminatorMap())))
            //->writeIf($lifecycleCallbacks, ' * @HasLifecycleCallbacks')
            ->write( ' * @ORM\HasLifecycleCallbacks()')
            ->write(' */');


        if($this->tableisAuthentication()){
            $writer->write('class '.$this->getClassName($extendableEntity).$extendsClass.$implementsInterface." implements UserInterface, \Serializable ,AdvancedUserInterface");
        }else{
            $writer->write('class '.$this->getClassName($extendableEntity).$extendsClass.$implementsInterface);
        }

        $writer->write('{')
            ->indent()
            ->writeCallback(function(WriterInterface $writer, Table $_this = null) use ($skipGetterAndSetter, $serializableEntity, $lifecycleCallbacks) {
                $_this->writePreClassHandler($writer);
                $_this->writeVars($writer);
                $_this->writeConstructor($writer);
                if (!$skipGetterAndSetter) {
                    $_this->writeGetterAndSetter($writer);
                }
                $_this->writePostClassHandler($writer);
                foreach ($lifecycleCallbacks as $callback => $handlers) {
                    foreach ($handlers as $handler) {
                        $writer
                            ->write('/**')
                            ->write(' * @%s', ucfirst($callback))
                            ->write(' */')
                            ->write('public function %s()', $handler)
                            ->write('{')
                            ->write('}')
                            ->write('')
                        ;
                    }
                }
                if ($serializableEntity) {
                    $_this->writeSerialization($writer);
                }
            })
            ->outdent()
            ->write('}')
            ->close()
        ;
        if ($extendableEntity && !$writer->getStorage()->hasFile($this->getClassFileName())) {
            $writer
                ->open($this->getClassFileName())
                ->write('<?php')
                ->write('')
                ->writeCallback(function(WriterInterface $writer, Table $_this = null) {
                    if ($_this->getConfig()->get(Formatter::CFG_ADD_COMMENT)) {
                        $writer
                            ->write($_this->getFormatter()->getComment(Comment::FORMAT_PHP))
                            ->write('')
                        ;
                    }
                })
                ->write('namespace %s;', $namespace)
                ->write('')
                ->writeCallback(function(WriterInterface $writer, Table $_this = null) {
                    $_this->writeExtendedUsedClasses($writer);
                });

            $writer->write('/**')
                ->write(' * '.$this->getNamespace(null, false))
                ->write(' *')
                ->write(' * '.$this->getAnnotation('Entity', array('repositoryClass' => $this->getConfig()->get(Formatter::CFG_AUTOMATIC_REPOSITORY) ? $repositoryNamespace.$this->getModelName().'Repository' : null)))
                ->write(' */')
                ->write('class %s extends %s', $this->getClassName(), $this->getClassName(true))
                ->write('{')
                ->write('}')
                ->close()
            ;
        }
    }

    public function writeConstructor(WriterInterface $writer)
    {
        $writer
            ->write('public function __construct()')
            ->write('{')
            ->indent()
            ->writeCallback(function(WriterInterface $writer, Table $_this = null) {
                $_this->writeRelationsConstructor($writer);
                $_this->writeManyToManyConstructor($writer);
            })
            ->outdent()
            ->write('}')
            ->write('')
        ;

        $arr=$this->getColumns()->getColumnNames();
        if(count($arr)>0){
            $caden=(in_array("nombre",$arr))?"$"."this->nombre;":"(string) $"."this->{$arr[0]};";

            $writer
                ->write('public function __toString()')
                ->write('{')
                ->indent()
                ->write('return '.$caden)
                ->outdent()
                ->write('}')
                ->write('')
            ;
        }

        $writer
            ->write('/**')
            ->write('* Gets triggered only on insert')
            ->write('* @ORM\PrePersist')
            ->write('*/')
            ->write('public function onPrePersist(){')
            ->indent();
        if(in_array("creado",$arr) ){
            $writer->write('$this->creado = new \DateTime("now"); ');
        }elseif(in_array("creada",$arr) ){
            $writer->write('$this->creada = new \DateTime("now"); ');
        }else{
            $writer->write('//Cambiar por el campo creado si es que existe en la tabla');
            $writer->write('//$this->created = new \DateTime("now"); ');
        }
        $writer->outdent()
            ->write('}')
            ->write('')
        ;

        $writer
            ->write('/**')
            ->write('* Gets triggered only on update')
            ->write('* @ORM\PreUpdate')
            ->write('*/')
            ->write('public function onPreUpdate(){')
            ->indent();
        if(in_array("modificado",$arr) ){
            $writer->write('$this->modificado = new \DateTime("now"); ');
        }elseif(in_array("actualizado",$arr) ){
            $writer->write('$this->actualizado = new \DateTime("now"); ');
        }else{
            $writer->write('//Cambiar por el campo modificado si es que existe en la tabla');
            $writer->write('//$this->updated = new \DateTime("now"); ');
        }
        $writer->outdent()
            ->write('}')
            ->write('')
        ;

        return $this;
    }

    /**
     * Quote identifier if necessary.
     *
     * @param string $value  The identifier to quote
     * @return string
     */
    public function quoteIdentifier($value,$iscolum=true)
    {
        $quote = false;
        switch ($this->getConfig()->get(Formatter::CFG_QUOTE_IDENTIFIER_STRATEGY)) {
            case Formatter::QUOTE_IDENTIFIER_AUTO:
                $quote = ReservedWords::isReserved($value);
                break;

            case Formatter::QUOTE_IDENTIFIER_ALWAYS:
                $quote = true;
                break;
        }
        if($iscolum==false){
            if(substr($value, -1)=='a' || substr($value, -1)=='e' || substr($value, -1)=='i' || substr($value, -1)=='o' || substr($value, -1)=='u'){
                $value.='s';
            }elseif(substr($value, -1)=='l' || substr($value, -1)=='n'  || substr($value, -1)=='r' || substr($value, -1)=='d'){
                $value.='es';
            }
        }

        return $quote ? '`'.$value.'`' : $value;
    }

    public function writeSerialization(WriterInterface $writer)
    {
        $writer
            ->write('public function __sleep()')
            ->write('{')
            ->indent()
            ->write('return array(%s);', implode(', ', array_map(function($column) {
                return sprintf('\'%s\'', $column);
            }, $this->getColumns()->getColumnNames())))
            ->outdent()
            ->write('}')
        ;
        if($this->tableisAuthentication()){
            $this->writeImplementMetodos($writer);
        }

        return $this;
    }

    public function tableisAuthentication(){
        return (in_array($this->getModelName(),array("Administradores", "Usuarios","Administrador", "Usuario","Users") ));
    }

    public function writeImplementMetodos(WriterInterface $writer){
        $columnas=$this->getColumns()->getColumnNames();
        $col_email=(in_array("email",$columnas))?"email":((in_array("mail",$columnas))?"mail":"correo");
        $col_id ='id';
        $rol=($this->getModelName()=="Administradores")?"ROLE_ADMIN":"ROLE_LOCAL";
        $writer
            ->write('')
            ->write('public function getUsername()')
            ->write('{')
            ->write('    return $this->'.$col_email.';')
            ->write('}')
            ->write('')
            ->write('public function getSalts()')
            ->write('{')
            ->write("    return '';")
            ->write('}')
            ->write('')
            ->write('public function getPassword()')
            ->write('{')
            ->write('    return $this->pass;')
            ->write('}')
            ->write('')
            ->write('public function getRoles()')
            ->write('{')
            ->write("    return array('".$rol."');")
            ->write('}')
            ->write('')
            ->write('public function eraseCredentials()')
            ->write('{')
            ->write('}')
            ->write('')
            ->write('/** @see \Serializable::serialize() */')
            ->write('public function serialize()')
            ->write('{')
            ->write('    return serialize(array(')
            ->write('            $this->'.$col_id.',')
            ->write('            $this->'.$col_email.',')
            ->write('            $this->pass,')
            ->write('            // see section on salt below')
            ->write('            // $this->salt,')
            ->write('            $this->activo')
            ->write('        ));')
            ->write('}')
            ->write('')
            ->write('/** @see \Serializable::unserialize() */')
            ->write('public function unserialize($serialized)')
            ->write('{')
            ->write('    list (')
            ->write('        $this->'.$col_id.',')
            ->write('        $this->'.$col_email.',')
            ->write('        $this->pass,')

            ->write('        // see section on salt below')
            ->write('        // $this->salt')
            ->write('        $this->activo')
            ->write('        ) = unserialize($serialized);')
            ->write('}')
            ->write('')
            ->write('public function isAccountNonExpired()')
            ->write('{')
            ->write('    return true;')
            ->write('}')
            ->write('')
            ->write('public function isAccountNonLocked()')
            ->write('{')
            ->write('    return true;')
            ->write('}')
            ->write('')
            ->write('public function isCredentialsNonExpired()')
            ->write('{')
            ->write('    return true;')
            ->write('}')
            ->write('')
            ->write('public function isEnabled()')
            ->write(' { ')
            ->write('    return $this->activo;')
            ->write(' } ');
    }

}
