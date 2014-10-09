#load packages (manually preinstalled)
library("raster")
library("rgdal")
#library("maptools")
#library("sp")
library('leafletR')

#read inputs
readIn <- commandArgs(trailingOnly = TRUE)

in_pShapefile <- readIn[1]
in_fShapefile <- readIn[2]
in_pRaster <- readIn[3]
in_fRaster <- readIn[4]
in_pCache <- readIn[5]
in_fCache <- readIn[6]
in_pBase <- readIn[7]

#prepare paths
#dir_base <- "G:/xampp/htdocs/gis/DET_03/resources/"
#dir_base <- "/var/www/html/DET_03/resources/"
dir_base <- paste(in_pBase, "/resources/", sep="")
dir_shapefile <- paste(dir_base, in_pShapefile, sep="")
dir_raster <- paste(dir_base, in_pRaster, sep="")
dir_cache <- paste(dir_base, in_pCache, sep="")
dir_geojson <- paste(dir_cache, "/geojsons", sep="")

#load shapefile
setwd(dir_shapefile)
#myVector <- readShapeSpatial(in_fShapefile, proj4string=CRS("+proj=longlat +datum=WGS84 +no_defs"))
myVector <- readOGR(dir_shapefile, in_fShapefile)

#load raster
setwd(dir_raster)
myRaster <- raster(in_fRaster, crs="+proj=longlat +datum=WGS84 +no_defs")
myRaster[is.na(myRaster)] <- 0 

#extract raster data
myExtract <- extract(disaggregate(myRaster, fact=c(4,4)), myVector, fun=mean, sp=TRUE, weights=TRUE, small=TRUE)
myOutput <- myExtract@data

#output to csv
setwd(dir_cache)
write.table(myOutput, in_fCache, quote=F, row.names=F, sep=",")

#create geojson for mapping applications
setwd(dir_geojson)
toGeoJSON(data=myExtract, name=unlist(strsplit(in_fCache, "[.]"))[1])