#setwd("F:/xampp/htdocs/gis/DET_03/rasterUpload")
# setwd("/var/www/html/aiddata/DET/rasterUpload")
# write("00", file="test.txt")

#load packages
library("raster")
library("rgdal")

#read inputs
readIn <- commandArgs(trailingOnly = TRUE)
in_pRaster <- readIn[1]
in_fRaster <- readIn[2]
in_pShapefile <- readIn[3]
in_fShapefile <- readIn[4]
in_pOutput <- readIn[5]
in_fOutput <- readIn[6]
in_pBase <- readIn[7]

#prepare paths
dir_base <- in_pBase
dir_raster <- paste(dir_base, in_pRaster, sep="")
dir_shapefile <- paste(dir_base, in_pShapefile, sep="")
dir_output <- paste(dir_base, in_pOutput, sep="")

#load raster
setwd(dir_raster)
myRaster <- raster(in_fRaster, crs="+proj=longlat +datum=WGS84 +no_defs")

#load shapefile
setwd(dir_shapefile)
myVector <- readOGR(dir_shapefile, in_fShapefile)

#crop raster data
setwd(dir_output)
myCrop <- crop(myRaster, myVector, filename=in_fOutput)

