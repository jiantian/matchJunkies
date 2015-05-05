import sys
import subprocess
import os
import math
import shutil
import numpy as np
from itertools import groupby
import re

def team_list(teamfile):
	teams = []
	f_in = open(teamfile, 'r')
	for line in f_in:
		teams.append(line.strip())
	f_in.close()
	return teams

def url(team1, team2):
	link = "http://www.11v11.com/teams/"+team1.lower().replace(" ","-") \
			+"/tab/opposingTeams/opposition/"+team2.replace(" ","%20")
	return link

def getMatchLinks(recordFile):
	lookup = "http://www.11v11.com/matches"
	links = []
	myFile = open(recordFile, 'r')
	for line in myFile:
		if lookup in line:
			links.append(line.strip())
	myFile.close()
	return links

def divideScorer(goals):
	groupby(goals, lambda x: x == "Goals:")
	fullScorer = [list(group) for k, group in groupby(goals, lambda x: x == "Goals:") if not k]
	return fullScorer

def writeTopScorer(scorer, f_out, opponent):
	with open(f_out, "a") as myfile:
		myfile.write("\n")
		myfile.write("Top scorer against "+opponent+":\n")
		if not scorer == {}:
			highest = max(scorer.values())
			players = [k for k,v in scorer.items() if v == highest]
			for player in players:
				myfile.write(player+"\t"+str(scorer[player])+"\n")
		myfile.write("\n")

def getScorer(matches, team):
	scorers = {}
	for matchURL in matches:
		command = ["phantomjs","text-scraper.js",matchURL,"match.txt"]
		subprocess.check_call(command)
		myFile = open('match.txt', 'r')
		lines = myFile.readlines()
		opponents = lines[1].strip().split(" v ")
	
		goals = []
		# Get scorers for both teams
		parsing = False
		for line in lines:
			if line.startswith("Starting lineup:"):
				parsing = False
			if parsing:
				str = line.strip().split("\t")
				if not (len(str) > 1 and str[1] == ' own goal'):
					goals.append(str[0])
			if line.startswith("Goals:"):
				parsing = True

		if len(divideScorer(goals)) == 2:		
			if team in opponents[0]:    #then we want the items before "Goals:" 
				teamScorer = divideScorer(goals)[0]
			else:
				teamScorer = divideScorer(goals)[1]
		elif len(divideScorer(goals)) == 1:
			if team in opponents[0]:    #then we want the items before "Goals:"
				if goals[0] == 'Goals:':
					teamScorer = []
				elif goals[-1] == 'Goals:':
					teamScorer = divideScorer(goals)[0]
			else:
				if goals[0] == 'Goals:':
					teamScorer = divideScorer(goals)[0]
				elif goals[-1] == 'Goals:':
					teamScorer = []
		else:
			teamScorer = []

		for item in teamScorer:
			if item in scorers:
				scorers[item] += 1
			else:
				scorers[item] = 1

		myFile.close()
	return scorers

def longestStreak(f_in):
	str = ''
	dates = []
	f = open(f_in, 'r')
	lines = f.readlines()
	parsing = False
	for line in lines:
		if line.strip() == '':
			parsing = False
		if parsing:
			str1 = line.strip().split("\t")
			if len(str1) > 1:
				dates.append(str1[0])
				str	+= str1[2]
		if line.startswith("Date"):
			parsing = True
	f.close()

	if 'W' in str:
		if 'WW' in str:
			longestWin = len(max(re.compile("(W+W)").findall(str)))
		else:
			longestWin = 1
	else:
		longestWin = 0
	if 'L' in str:
		if 'LL' in str:
			longestLoss = len(max(re.compile("(L+L)").findall(str)))
		else:
			longestLoss = 1
	else:
		longestLoss = 0
	str_win = 'W' * longestWin
	str_loss = 'L' * longestLoss
	dates_win = []
	dates_loss = []
	if longestWin > 0:
		starts_win = [match.start() for match in re.finditer(re.escape(str_win), str)]
		for index in starts_win:
			dates_win.append(dates[index:(index+longestWin)])
	if longestLoss > 0:
		starts_loss = [match.start() for match in re.finditer(re.escape(str_loss), str)]
		for index in starts_loss:
			dates_loss.append(dates[index:(index+longestLoss)])

	return longestWin, longestLoss, dates_win, dates_loss

def writeStreak(f_in, f_out):
	longestWin, longestLoss, dates_win, dates_loss = longestStreak(f_in)
	with open(f_out, "a") as myfile:
		myfile.write("Longest winning streak: "+str(longestWin)+"\n")
		myfile.write("for the matches on:\n")
		for item in dates_win:
			for entry in item:
				if entry == item[-1]:
					myfile.write(entry)
				else:
					myfile.write(entry+", ")
			myfile.write("\n")
		myfile.write("\n")
		myfile.write("Longest losing streak: "+str(longestLoss)+"\n")
		myfile.write("for the matches on:\n")
		for item in dates_loss:
			for entry in item:
				if entry == item[-1]:
					myfile.write(entry)
				else:
					myfile.write(entry+", ")
			myfile.write("\n")

teams = team_list("teams.txt")
counter = 0
for	i in range(0, len(teams)):
	team1 = teams[i]
	print "Crawl the match records for "+team1+":"
	for j in range(0, len(teams)):
		if i != j:
			team2 = teams[j]
			record_url = url(team1, team2)
			counter += 1
			f_out_name = "records/"+str(counter) + ".txt"
			command = ["phantomjs","text-scraper.js",record_url,f_out_name]
			subprocess.check_call(command)
			matchLinks = getMatchLinks(f_out_name)
			scorers = getScorer(matchLinks, team1)
			writeTopScorer(scorers, f_out_name, team2)
			writeStreak(f_out_name, f_out_name)
