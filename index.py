import sys
import shutil
import numpy as np
from itertools import groupby
import os

def team_list(teamfile):
	teams = []
	f_in = open(teamfile, 'r')
	for line in f_in:
		teams.append(line.strip())
	f_in.close()
	return teams

def teamName(teams_list):
	team = ''
	for item in teams_list:
		team += item
		team += ' '
	return team.strip() 

def parseTeams(f_in):
	f = open(f_in, 'r')
	line = f.readlines()[1].strip().split(" ")
	groupby(line, lambda x: x == "national")
	a = [list(group) for k, group in groupby(line, lambda x: x == "national") if not k]
	team1_list = a[0]
	line2 = a[1]
	groupby(line2, lambda x: x == "v")
	team2_list = [list(group) for k, group in groupby(line2, lambda x: x == "v") if not k][1]
	team1 = teamName(team1_list)
	team2 = teamName(team2_list)
	f.close()
	return team1, team2

def validate(f_in):
	f = open(f_in, 'r')
	line = f.readlines()[1]	
	f.close()
	if line.startswith('404'):
		return False
	else:
		return True	

def invertedIndex(team, files):
	strout = [team]
	for fname in files:
		if validate(fname):
			team1, team2 = parseTeams(fname)
			name = fname.split(".")[0].split("/")[1]
			if team.lower() == team1.lower():
				strout.append(name)
				strout.append(str(1))
			if team.lower() == team2.lower():
				strout.append(name)
				strout.append(str(-1))
	return strout

teams = team_list('teams.txt')
path = "records/"
files = [ path+f for f in os.listdir(path) if f.endswith(".txt") ]
f_out = open('invertedIndex.txt', 'w')
for team in teams:
	teamIndex = invertedIndex(team, files)
	for item in teamIndex:
		f_out.write(item+"\t")
	f_out.write("\n")
f_out.close()
