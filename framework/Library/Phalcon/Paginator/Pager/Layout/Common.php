<?php
/**
 * 91160网通用分页样式
 * Phalcon Framework
 * This source file is subject to the New BSD License that is bundled
 * with this package in the file docs/LICENSE.txt.
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@phalconphp.com so we can send you a copy immediately.
 *
 * @author Nikita Vershinin <endeveit@gmail.com>
 */
namespace Phalcon\Paginator\Pager\Layout;

use Phalcon\Paginator\Pager\Layout;

/**
 * \Phalcon\Paginator\Pager\Layout\Bootstrap
 * Pager layout that uses Twitter Bootstrap styles.
 */
class Common  extends Layout
{

    /**
     *
     * @ERROR!!!
     *
     * @var string
     */
    protected $template = '<a href="{%url}" class="n">{%page}</a>';

    /**
     *
     * @ERROR!!!
     *
     * @var string
     */
    protected $selectedTemplate = '<strong>{%page}</strong>';

    /**
     *
     * @ERROR!!!
     *
     * @param array $options            
     * @return string
     */
    public function getRendered(array $options = array())
    {
        $result = "<p id='s_pager' class='page'>";
        $bootstrapSelected = '<span>{%page}</span>';
        $originTemplate = $this->selectedTemplate;
        $this->selectedTemplate = $bootstrapSelected;

        if($this->pager->getCurrentPage() > $this->pager->getFirstPage()){
            $this->addMaskReplacement('page', '首页', true);
            $options['page_number'] = $this->pager->getFirstPage();
            $result .= $this->processPage($options);
            
            $this->addMaskReplacement('page', '上一页', true);
            $options['page_number'] = $this->pager->getPreviousPage();
            $result .= $this->processPage($options);
        }
        $this->selectedTemplate = $originTemplate;
        $this->removeMaskReplacement('page');
        $result .= parent::getRendered($options);
        
        $this->selectedTemplate = $bootstrapSelected;
        
        if($this->pager->getCurrentPage() < $this->pager->getLastPage()){
            $this->addMaskReplacement('page', '下一页', true);
            $options['page_number'] = $this->pager->getNextPage();
            $result .= $this->processPage($options);        
            
            $this->addMaskReplacement('page', '尾页', true);
            $options['page_number'] = $this->pager->getLastPage();
            $result .= $this->processPage($options);
        }
        $this->selectedTemplate = $originTemplate;        
        $result .= '</p>';        
        return $result;
    }
}
