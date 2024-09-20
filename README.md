# W2G-sync
A plugin to automatically sync WordPress blog posts to GitHub. 

# 功能
1. 把新发布文章直接同步到github指定的仓库
2. 老文章快捷菜单“Push to GitHub”一键推送到github仓库

# 使用条件
1. 有一个wordpress博客
2. 有一个github账户

# 使用方法
1. 下载插件压缩包安装到wordpress博客并激活
2. 设置方法
   2.1. 在wordpress后台点击【设置】-【WP to GitHub Sync】进入设置界面
   ![image](https://github.com/user-attachments/assets/e56d8ce7-2d65-439f-80ae-49de2bffbdb5)

   2.2. 在设置项填入对应的 GitHub Personal Access Token 和 GitHub Repository ，注意：GitHub Repository 结构是【用户名/仓库名】，如 zimiyu/W2G-sync .
   2.3. 保存，然后新发布文章就会一markdown的格式同步到2.2指定的仓库中.
3. GitHub Personal Access Token 的获取方法请参考github文档.
   
 
