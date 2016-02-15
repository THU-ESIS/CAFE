# CAFE
Latest CAFE packages
### Welcome to the CAFE wiki!
This document is written by [Hao Xu](https://www.researchgate.net/profile/Hao_Xu31).      
The authors of CAFE include [Yuqi Bai](http://yuqibai.net), [Hao Xu](https://www.researchgate.net/profile/Hao_Xu31), Ke Yan, Chang Xiao, Sha Li etc.    
If you have any questions about CAFE, please send email to us.    
Email:xuhao13@mails.tsinghua.edu.cn      
Address:Center for Earth System Science,Tsinghua University,Beijing,China,100084     
CAFE includes THREE Repositories: [CAFE_NODE](https://github.com/THU-EarthInformationScienceLab/CAFE_NODE),[CAFE_SCRIPTS](https://github.com/THU-EarthInformationScienceLab/CAFE_SCRIPTS) and [CAFE_PORTAL](https://github.com/THU-EarthInformationScienceLab/CAFE_PORTAL)


##Introduction
Environmental science study has experienced considerable revolutions in recent years due to significant advances in high performance computing, storage and networking technologies.To assist scientists to work efﬁciently with distributed datasets, and to allow them to focus their efforts on their research rather than prepare data and write repetitive analytic scripts, we propose Collaborative Analysis Framework for Environmental data (CAFE), a Java-based distributed framework. We provide a server-side package to be installed on each data node and a web user interface package configured for accessing of end users. Multiple data nodes could collaborate with each other to fulfil concurrent multi-node data analysis work. Scientists can discover their interested data, submit analytic tasks and retrieve analysis results from the web user interface. CAFE aims to make scientists easily discover and analyze distributed data from multiple sources as if they are using data stored in a local centralized archive and provide extensible solutions for analytic method sharing.

##Architecture
CAFE is supposed to enable efficient data query and large scale collaborative analysis of environmental data. To fulfil this design goal, CAFE is designed to mainly include four parts: data index module, task managing module, data analysis module and Web-based user interface. Besides, there is a central server used for the management of global nodes and data. All the nodes absorbed in CAFE network are peer to peer and united by common protocols and interfaces. CAFE consists of a server-side package and a web user interface package. The server-side package integrates index module, task managing module and data analysis module, and will be installed at each node. The web user interface package includes the web user interface and related services, can be configured at any web server.
![My Unicorn](http://ww1.sinaimg.cn/mw690/46083cc0jw1ez65heigo7j20xz0kutbd.jpg)

##Workflow
A typical scientific data analysis workflow in CAFE consists of the following steps: 1) the user selects datasets and analytic function and submit the analysis task through the Web-based user interface; 2) the task managing module parses the task and decomposes it into several sub-tasks, where one dataset corresponds to one sub-task; 3) all the sub-tasks are dispatched to the nodes where the datasets are stored; 4) all the sub-tasks are fulfilled by invoking the corresponding analytic functions locally; 5) after all the sub-tasks are finished, the task managing module collects the results and sends compiled analysis results back to the user through the Web-based user interface; 6) the user can download the analysis results in the last step.        

![Design of Major module in CAFE](http://ww3.sinaimg.cn/mw690/46083cc0jw1ez6727qd5xj20s60jxq8v.jpg)

##Web user interface
The web-based user interface provides a user-friendly way for researchers to query and analyze environmental data through the web browser.It mainly includes six parts, respectively are initialization, search, task submission, task list checking, task detail checking and results retrieval. If a user wants to use analytic functions in CAFE he has to register an account first, and then log in. The user can filter the dataset by specifying institute, project, frequency, etc. After selecting desired datasets to the list, the application will send a request to the node and obtain the information of available analytic functions. The user can choose the analytic function, set the corresponding parameters and submit a task.
When the user submits the task form, the information of seleted analytic function will be queried and used to validate input parameters. The application regularly sends requests to the node to retrieve real-time status of tasks by APIs. Asynchronous refreshing is utilized to show real-time status of each task. The user can compare the results of different sub-tasks from the task detail page and download different kinds of results. The web page will demonstrate the graphs or the charts of the results and provide download links of multi-format result files.       

![Design of web user interface](http://ww3.sinaimg.cn/mw690/46083cc0jw1ez7d8wdwysj20rn0jg0x0.jpg)
