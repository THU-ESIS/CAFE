;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
;;;;;;;;;;;;;;;;;;;;;Editor: Eric Xu;;;;;;;;;;;;;;;;;;;;;;;
;;;;;;;;;;;;;;;;;;;;;Date: 2015.04.03;;;;;;;;;;;;;;;;;;;;;;
;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
load "$NCARG_ROOT/lib/ncarg/nclscripts/csm/gsn_code.ncl"
load "$NCARG_ROOT/lib/ncarg/nclscripts/csm/gsn_csm.ncl"
load "$NCARG_ROOT/lib/ncarg/nclscripts/csm/contributed.ncl"
load "$NCARG_ROOT/lib/ncarg/nclscripts/csm/shea_util.ncl"
begin
;************************************************
; Define parameters
;************************************************
   if(.not.isvar("fi_data")) then
   	fi_data = "/mnt/CMIP5/cmip5_tmp/1010/seas_data"
   end if
   if(.not.isvar("fi_name")) then
   	fi_name = "sic_OImon_FGOALS-g2_historicalGHG_r1i1p1_192701-199112_seas.nc"
   end if
   if(.not.isvar("fig_data")) then
   	fig_data = "/mnt/CMIP5/cmip5_tmp/1010/fig_data"
   end if
   if(.not.isvar("fig_name")) then
   	fig_name = "sic_OImon_FGOALS-g2_historicalGHG_r1i1p1_192701-199112_TS_SEAS__45_90_160_260"
   end if
   if(.not.isvar("txt_data")) then
   	txt_data = "/mnt/CMIP5/cmip5_tmp/1010/txt_data"
   end if
   if(.not.isvar("txt_name")) then
   	txt_name = "sic_OImon_FGOALS-g2_historicalGHG_r1i1p1_192701-199112_TS_SEAS__45_90_160_260.txt"
   end if
   if(.not.isvar("var_name")) then
   	var_name="sic"
   end if
   if(.not.isvar("fig_type")) then
   	fig_type= "png"
   end if
   if(.not.isvar("start_year")) then
   	start_year=1927
   end if
   if(.not.isvar("end_year")) then
   	end_year=1991
   end if
   if(.not.isvar("latmin")) then
   	latmin=45
   end if
   if(.not.isvar("latmax")) then
   	latmax=90
   end if
   if(.not.isvar("lonmin")) then
  	lonmin=160
   end if
   if(.not.isvar("lonmax")) then
   	lonmax=260
   end if

   
   ;linecolor   =   "red"
   ;linethick   =   2.0
   ;linedash    =   0
   xmin      =   start_year
   xmax      =   end_year
   ns        =   4
;************************************************
; process netCDF file
;************************************************

    fi      =   addfile(fi_data+"/"+fi_name,"r") 
    dat0     =   fi->$var_name$
    var0= dat0(time|:,lat|:,lon|:)
    dat = var0(:,{latmin:latmax},{lonmin:lonmax})
    time    =   cd_calendar(dat0&time,-1)
    mn      =   dimsizes(time) 
    nt      =   mn/4
;print(nt)
    n = -1
    YYYY = new(nt,integer)
    do i=0,mn-2,4               ;原mn个时间点剔除最后一个时间点，故下标取到mn-2
       n = n+1
       YYYY(n) = time(i)/100
    end do
;print(YYYY)

    var     = new((/2,nt,ns/),double)
;计算区域平均，返回值是n-2维,此案例得到随时间变化的一维数组
    var_mean = wgt_areaave(dat,1.0,1.0,0) 
;printVarSummary(var_mean)
    do j  =  0,ns-1
       do i =  0,nt-1
          var(0,i,j)  = var_mean(4*i+j) 
       end do
    end do

;计算一元线性回归
    rc   =  new(ns,double)
    yave   =  new(ns,double)
    expression = new(ns,string)
    do j = 0,ns-1 
    rc(j)            =   regline(YYYY,var(0,:,j))    ;四季斜率(包含其他相关属性)
;print(rc)
    yave(j)          =   dim_avg(var(0,:,j))
    var(1,:,j)       =   rc(j)*(YYYY-rc@xave) + yave(j)
    expression(j)    =   "y="+tostring(rc(j))+"x"+"+"+tostring(yave(j))
    end do
    x_title     =   "year"
;************************************************
; create plot
;************************************************ 
    
    wks = gsn_open_wks(fig_type, fig_data+"/"+fig_name)
    plot = new(4,graphic)
    res =   True

    res@gsnDraw   = False 
    res@gsnFrame  = False                  ; don't advance frame yet 
    ;res@gsnMaximize = True

    res@vpHeightF = 0.5        ; Changes the aspect ratio
    res@vpWidthF  = 0.75
    res@trXMinF   = xmin
    res@trXMaxF   = xmax

    res@xyMarkLineModes     = (/"MarkLines","Lines"/)            ; choose which have markers
    res@xyMarkers           = 16                     ; choose type of marker 
    res@xyMarkerColor       = "red"                  ; Marker color
    res@xyMarkerSizeF       = 0.005                  ; Marker size (default 0.01)
    res@xyLineThicknesses      = 2
    res@xyLineColors        = (/"black","blue"/)
    res@xyDashPatterns      = (/0,16/)
    
     
    res@tiYAxisString       =       ""
    res@gsnLeftString       =       ""
    res@gsnRightString      =       ""
    res@tiXAxisString       =      x_title
    res@tiYAxisString       =      dat0@long_name+" ("+dat0@units +" )"

    res@pmLegendDisplayMode  = "Always"
    res@lgPerimOn            = False      ; No legend perimeter.
    res@pmLegendWidthF       =    0.15  ; change the width
    res@pmLegendHeightF      =    0.05  ; change the height
    res@xyExplicitLegendLabels = (/"var_mean","trend"/)

    res@pmLegendZone           = 0 
    res@pmLegendParallelPosF    = 0.35
    res@pmLegendOrthogonalPosF  = -0.42
    res@lgLabelFontHeightF=0.022

    do i = 0,ns-1
       res@gsnCenterString     = expression(i)       
       plot(i)    =   gsn_csm_xy(wks,YYYY,var(:,:,i),res)
    end do

    resP    =  True
    resP@txString       =     "Seasonal Time Series of "+dat0@long_name+" ("+ start_year +" to "+end_year+" )"

    resP@gsnPanelFigureStrings  = (/"a)Winter","b)Spring","c)Summer","d)Autumn"/) 
    resP@gsnPanelFigureStringsPerimOn = False
    resP@gsnPanelFigureStringsFontHeightF  = 0.015
    resP@amJust = "BottomLeft" 



    gsn_panel(wks,plot,(/2,2/),resP)
    header = (/"-----------------------------------------", \
               "year  winter   spring   summer   autumn", \
               "-----------------------------------------"/)
    footer = (/"--------------------------------", \
            "WINTER:"+expression(0), \
            "SPRING:"+expression(1), \
            "SUMMER:"+expression(2), \
            "AUTUMN:"+expression(3), \
            "--------------------------------"/)
    alist = [/YYYY,var(0,:,0),var(0,:,1),var(0,:,2),var(0,:,3)/]
    hlist = [/header/]
    flist = [/footer/]

    write_table(txt_data+"/"+txt_name, "w", hlist, "%s")
    write_table(txt_data+"/"+txt_name, "a", alist, "%d %f %f %f %f")
    write_table(txt_data+"/"+txt_name, "a", flist, "%s")
    delete(plot)
    delete(res)
    delete(resP) 
    

end

    
